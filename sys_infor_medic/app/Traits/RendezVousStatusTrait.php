<?php

namespace App\Traits;

trait RendezVousStatusTrait 
{
    /**
     * Normalise le statut de rendez-vous
     */
    public function normalizeStatus($status)
    {
        if (empty($status)) return 'en_attente';
        
        $status = strtolower(trim($status));
        
        // Mapping des statuts
        $statusMapping = [
            'en_attente' => 'en_attente',
            'en attente' => 'en_attente', 
            'pending' => 'en_attente',
            'attente' => 'en_attente',
            
            'confirmé' => 'confirmé',
            'confirme' => 'confirmé',
            'confirmed' => 'confirmé',
            'confirmée' => 'confirmé',
            'confirmee' => 'confirmé',
            
            'annulé' => 'annulé',
            'annule' => 'annulé',
            'canceled' => 'annulé',
            'cancelled' => 'annulé',
            'annulée' => 'annulé',
            'annulee' => 'annulé',
            
            'terminé' => 'terminé',
            'termine' => 'terminé',
            'completed' => 'terminé',
            'terminée' => 'terminé',
            'terminee' => 'terminé'
        ];
        
        return $statusMapping[$status] ?? 'en_attente';
    }
    
    /**
     * Obtient la classe CSS pour le statut
     */
    public function getStatusBadgeClass($status)
    {
        $normalizedStatus = $this->normalizeStatus($status);
        
        return match($normalizedStatus) {
            'confirmé' => 'bg-success',
            'annulé' => 'bg-secondary',
            'terminé' => 'bg-info',
            default => 'bg-warning text-dark'
        };
    }
    
    /**
     * Obtient le libellé formaté du statut
     */
    public function getStatusLabel($status)
    {
        $normalizedStatus = $this->normalizeStatus($status);
        
        return match($normalizedStatus) {
            'en_attente' => 'En attente',
            'confirmé' => 'Confirmé',
            'annulé' => 'Annulé', 
            'terminé' => 'Terminé',
            default => ucfirst(str_replace('_', ' ', $status))
        };
    }
    
    /**
     * Vérifie si un statut permet le paiement
     */
    public function canPayForStatus($status)
    {
        return $this->normalizeStatus($status) === 'confirmé';
    }
}