<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    private function ensureAuthorizedPair($user, $partner): void
    {
        $a = $user->role; $b = $partner->role;
        $allowed = false;
        // Secretaire avec tout le monde
        if ($a==='secretaire' || $b==='secretaire') {
            $allowed = true;
            // Admin uniquement avec secretaire
            if (($a==='admin' && $b!=='secretaire') || ($b==='admin' && $a!=='secretaire')) {
                $allowed = false;
            }
        }
        // Médecin <-> Infirmier autorisé
        if ((($a==='medecin' && $b==='infirmier') || ($a==='infirmier' && $b==='medecin'))){
            $allowed = true;
        }
        // Patient uniquement avec secretaire et si assigné
        if ($a==='patient' || $b==='patient') {
            // patient doit parler à un secretaire uniquement
            if (!(($a==='patient' && $b==='secretaire') || ($b==='patient' && $a==='secretaire'))) {
                $allowed = false;
            }
            // plus de contrainte d'assignation: le patient peut choisir n'importe quelle secrétaire
        }
        if (!$allowed) abort(403);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $partnerId = (int) $request->query('partner_id', 0);

        // Partenaires autorisés en fonction du rôle
        $partnersQuery = User::query();
        switch ($user->role) {
            case 'secretaire':
                $partnersQuery->whereIn('role', ['admin','medecin','infirmier','patient']);
                break;
            case 'admin':
                $partnersQuery->where('role','secretaire');
                break;
            case 'medecin':
                $partnersQuery->whereIn('role',['secretaire','infirmier']);
                break;
            case 'infirmier':
                $partnersQuery->whereIn('role',['secretaire','medecin']);
                break;
            case 'patient':
                $partnersQuery->where('role','secretaire');
                break;
            default:
                $partnersQuery->whereRaw('1=0');
        }
        $partners = $partnersQuery->orderBy('name')->get();

        if (!$partnerId && $partners->count()) {
            $partnerId = $partners->first()->id;
        }
        $partner = $partnerId ? User::find($partnerId) : null;

        $messages = collect();
        if ($partner) {
            $conv = Conversation::ensure($user->id, $partner->id);
            // Marquer les messages reçus comme lus
            Message::where('conversation_id',$conv->id)
                ->where('sender_id','!=',$user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = $conv->messages()->with('sender')->orderBy('created_at','asc')->paginate(30);
        }

        return view('chat.index', compact('user','partners','partner','messages'));
    }

    public function send(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'partner_id' => ['required','integer','exists:users,id'],
            'body' => ['nullable','string','max:2000'],
            'file' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp','max:4096'],
        ]);

        // Autorisations simples selon les règles
        $partner = User::findOrFail($data['partner_id']);
        if ($user->role !== 'secretaire' && $partner->role !== 'secretaire') {
            abort(403);
        }

        $conv = Conversation::ensure($user->id, $partner->id);

        $filePath = null; $fileType = null;
        if ($request->hasFile('file')) {
            $stored = $request->file('file')->store('chat_files','public');
            $filePath = \Storage::url($stored);
            $ext = strtolower($request->file('file')->getClientOriginalExtension());
            $fileType = in_array($ext, ['jpg','jpeg','png','webp']) ? 'image' : 'file';
        }
        if (!$filePath && empty($data['body'])) {
            return Redirect::back()->withErrors(['body' => 'Message ou fichier requis']);
        }

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $user->id,
            'body' => $data['body'] ?? null,
            'file_path' => $filePath,
            'file_type' => $fileType,
        ]);

        // Notification email au partenaire
        try {
            $partner->notify(new \App\Notifications\NewChatMessage($msg));
        } catch (\Throwable $e) {
            // silencieux
        }

        return Redirect::route('chat.index', ['partner_id' => $partner->id]);
    }

    public function unreadCount(Request $request)
    {
        $uid = Auth::id();
        $count = Message::whereNull('read_at')
            ->whereHas('conversation', function($q) use ($uid) {
                $q->where('user_one_id',$uid)->orWhere('user_two_id',$uid);
            })
            ->where('sender_id','!=',$uid)
            ->count();
        return response()->json(['count' => $count]);
    }

    public function messages(Request $request)
    {
        $user = Auth::user();
        $partnerId = (int) $request->query('partner_id');
        $partner = User::findOrFail($partnerId);
        $this->ensureAuthorizedPair($user, $partner);
        $conv = Conversation::ensure($user->id, $partner->id);
        // Marquer comme lus les messages entrants
        Message::where('conversation_id',$conv->id)
            ->where('sender_id','!=',$user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $msgs = $conv->messages()->with('sender')->orderBy('created_at','asc')->take(100)->get();
        $payload = $msgs->map(function($m){
            return [
                'id' => $m->id,
                'sender' => $m->sender->name,
                'body' => $m->body,
                'file_path' => $m->file_path,
                'file_type' => $m->file_type,
                'created_at' => $m->created_at->toDateTimeString(),
                'mine' => $m->sender_id === auth()->id(),
                'read' => !is_null($m->read_at),
            ];
        });
        return response()->json(['messages' => $payload]);
    }

    public function typing(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'partner_id' => ['required','integer','exists:users,id']
        ]);
        $partner = User::findOrFail($data['partner_id']);
        $this->ensureAuthorizedPair($user, $partner);
        $conv = Conversation::ensure($user->id, $partner->id);
        $min = min($user->id, $partner->id);
        $now = now();
        if ($user->id === $min) {
            $conv->typing_user_one_at = $now;
        } else {
            $conv->typing_user_two_at = $now;
        }
        $conv->save();
        return response()->json(['ok' => true]);
    }

    public function typingStatus(Request $request)
    {
        $user = Auth::user();
        $partnerId = (int)$request->query('partner_id');
        $partner = User::findOrFail($partnerId);
        $this->ensureAuthorizedPair($user, $partner);
        $conv = Conversation::ensure($user->id, $partner->id);
        $min = min($user->id, $partner->id);
        $otherTypingAt = ($user->id === $min) ? $conv->typing_user_two_at : $conv->typing_user_one_at;
        $isTyping = $otherTypingAt && now()->subSeconds(6)->lt($otherTypingAt);
        return response()->json(['typing' => (bool)$isTyping]);
    }
}
