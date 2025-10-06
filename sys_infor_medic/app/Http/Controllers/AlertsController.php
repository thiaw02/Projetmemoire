<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Rendez_vous;

class AlertsController extends Controller
{
    public function summary(Request $request)
    {
        $user = Auth::user();
        $uid = $user->id;

        // Conversations summary
        $convs = Conversation::where('user_one_id',$uid)->orWhere('user_two_id',$uid)->get();
        $messages = [];
        $messages_unread_total = 0;
        foreach ($convs as $conv) {
            $partnerId = $conv->user_one_id === $uid ? $conv->user_two_id : $conv->user_one_id;
            $partner = User::find($partnerId);
            $last = $conv->messages()->orderBy('created_at','desc')->first();
            $unread = $conv->messages()->whereNull('read_at')->where('sender_id','!=',$uid)->count();
            $messages_unread_total += $unread;
            if ($last) {
                $preview = $last->body ?: ($last->file_type === 'image' ? 'Image envoyée' : ($last->file_path ? 'Fichier envoyé' : ''));
                $messages[] = [
                    'partner_id' => $partnerId,
                    'partner_name' => $partner->name ?? 'Utilisateur',
                    'unread' => $unread,
                    'last_at' => optional($last->created_at)->toDateTimeString(),
                    'preview' => mb_strimwidth((string)$preview, 0, 80, '…', 'UTF-8'),
                ];
            }
        }

        // RDV requests (secretaire)
        $rdvRequests = [];
        $rdv_requests_count = 0;
        if ($user->role === 'secretaire') {
            $pending = Rendez_vous::with('patient','medecin')
                ->whereIn('statut', ['en_attente','en attente','pending'])
                ->whereHas('patient', function($q) use ($uid){
                    $q->where(function($qq) use ($uid){
                        $qq->where('secretary_user_id', $uid)
                           ->orWhereNull('secretary_user_id');
                    });
                })
                ->orderBy('date')->orderBy('heure')->take(8)->get();
            foreach ($pending as $rdv) {
                $rdvRequests[] = [
                    'id' => $rdv->id,
                    'patient' => ($rdv->patient->nom ?? $rdv->patient->user->name ?? '—') . ' ' . ($rdv->patient->prenom ?? ''),
                    'medecin' => $rdv->medecin->name ?? '—',
                    'date' => $rdv->date,
                    'heure' => $rdv->heure,
                    'statut' => $rdv->statut,
                ];
            }
            $rdv_requests_count = count($rdvRequests);
        }

        // RDV updates (patient)
        $rdvUpdates = [];
        $rdv_updates_count = 0;
        if ($user->role === 'patient') {
            $items = Rendez_vous::with('medecin')
                ->where('user_id',$uid)
                ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','annulé','annule','annulée','annulee'])
                ->orderBy('updated_at','desc')
                ->take(8)->get();
            foreach ($items as $it) {
                $rdvUpdates[] = [
                    'id' => $it->id,
                    'medecin' => $it->medecin->name ?? '—',
                    'date' => $it->date,
                    'heure' => $it->heure,
                    'statut' => $it->statut,
                    'updated_at' => optional($it->updated_at)->toDateTimeString(),
                ];
            }
            $rdv_updates_count = count($rdvUpdates);
        }

        $total_count = $messages_unread_total + $rdv_requests_count + $rdv_updates_count;

        return response()->json([
            'messages' => $messages,
            'messages_unread_total' => $messages_unread_total,
            'rdvRequests' => $rdvRequests,
            'rdvUpdates' => $rdvUpdates,
            'total_count' => $total_count,
        ]);
    }
}