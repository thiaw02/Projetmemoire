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
        $staffRoles = ['medecin','infirmier','secretaire'];

        // Admin uniquement avec secrétaire
        if (($a==='admin' && $b==='secretaire') || ($b==='admin' && $a==='secretaire')) {
            $allowed = true;
        }

        // Équipe médicale: discussion autorisée si même service
        if (in_array($a, $staffRoles, true) && in_array($b, $staffRoles, true)) {
            $allowed = ($user->service_id && $user->service_id === $partner->service_id);
        }

        // Patient ↔ Secrétaire: autorisé si la secrétaire appartient à un service du patient
        if ((($a==='patient' && $b==='secretaire') || ($b==='patient' && $a==='secretaire'))) {
            $patient = $a==='patient' ? $user->patient : $partner->patient;
            $secretaire = $a==='secretaire' ? $user : $partner;
            $allowed = $patient && $secretaire->service_id && $patient->services()->where('services.id', $secretaire->service_id)->exists();
        }

        if (!$allowed) abort(403);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $partnerId = (int) $request->query('partner_id', 0);

        // Partenaires autorisés en fonction du rôle, avec restriction sur les affectations
        $partners = collect();
        if ($user->role === 'secretaire') {
            // même service uniquement pour medecin/infirmier; patients des services correspondants
            $partners = User::whereIn('role', ['medecin','infirmier'])
                ->where('service_id', $user->service_id)
                ->orderBy('name')->get();
            // patients assignés au service de la secrétaire
            $patientIds = \App\Models\Patient::whereHas('services', function($q) use ($user){
                $q->where('services.id', $user->service_id);
            })->pluck('user_id');
            $partners = collect([$partners, User::whereIn('id', $patientIds)->get()])->flatten(1)->unique('id')->values();
        } elseif ($user->role === 'admin') {
            $partners = User::where('role','secretaire')->orderBy('name')->get();
        } elseif ($user->role === 'medecin') {
            $partners = User::whereIn('role',['infirmier','secretaire'])
                ->where('service_id', $user->service_id)
                ->orderBy('name')->get();
        } elseif ($user->role === 'infirmier') {
            $partners = User::whereIn('role',['medecin','secretaire'])
                ->where('service_id', $user->service_id)
                ->orderBy('name')->get();
        } elseif ($user->role === 'patient') {
            // Secrétaires des services du patient
            $serviceIds = $user->patient?->services()->pluck('services.id');
            $partners = $serviceIds ? User::where('role','secretaire')->whereIn('service_id',$serviceIds)->orderBy('name')->get() : collect();
        } else {
            $partners = collect();
        }

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
            'file' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,mp3,m4a,wav,webm,ogg','max:10240'],
        ]);

        // Vérification des autorisations alignée sur les autres endpoints
        $partner = User::findOrFail($data['partner_id']);
        $this->ensureAuthorizedPair($user, $partner);

        $conv = Conversation::ensure($user->id, $partner->id);

        $filePath = null; $fileType = null;
        if ($request->hasFile('file')) {
            $stored = $request->file('file')->store('chat_files','public');
            $filePath = \Storage::url($stored);
            $ext = strtolower($request->file('file')->getClientOriginalExtension());
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $fileType = 'image';
            } elseif (in_array($ext, ['mp3','m4a','wav','webm','ogg'])) {
                $fileType = 'audio';
            } else {
                $fileType = 'file';
            }
        }
        if (!$filePath && empty($data['body'])) {
            return Redirect::back()->withErrors(['body' => 'Message ou fichier requis']);
        }

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $user->id,
            'body' => $data['body'] ?? '',
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
