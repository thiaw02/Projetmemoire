<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->syncAssignments($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged(['service_id', 'role'])) {
            $this->syncAssignments($user);
        }
    }

    /**
     * Synchronise les affectations selon le service et le rôle.
     * - Si infirmier: lier à tous les médecins du même service
     * - Si médecin: lier à tous les infirmiers du même service
     * - Si pas de service: détacher toutes les affectations correspondantes
     */
    protected function syncAssignments(User $user): void
    {
        // S'assurer que le modèle User a bien les relations nurses()/doctors()
        if ($user->role === 'infirmier') {
            if (empty($user->service_id)) {
                // Plus de service -> retirer toutes les liaisons à des médecins
                $user->doctors()->detach();
                return;
            }

            // Tous les médecins du même service
            $doctorIds = User::where('role', 'medecin')
                ->where('service_id', $user->service_id)
                ->pluck('id')
                ->all();

            // Synchroniser les affectations de cet infirmier vers les médecins
            $user->doctors()->sync($doctorIds);
            return;
        }

        if ($user->role === 'medecin') {
            if (empty($user->service_id)) {
                // Plus de service -> retirer toutes les liaisons à des infirmiers
                $user->nurses()->detach();
                return;
            }

            // Tous les infirmiers du même service
            $nurseIds = User::where('role', 'infirmier')
                ->where('service_id', $user->service_id)
                ->pluck('id')
                ->all();

            // Synchroniser les affectations de ce médecin vers les infirmiers
            $user->nurses()->sync($nurseIds);
            return;
        }

        // Si le rôle change vers un rôle non concerné, s'assurer d'un nettoyage
        // Détacher des deux côtés si jamais il avait des liens précédemment
        $user->nurses()->detach();
        $user->doctors()->detach();
    }
}
