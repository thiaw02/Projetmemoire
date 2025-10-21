<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasPagination
{
    /**
     * Standardiser les paramètres de pagination
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => $this->getPerPage($request),
            'page' => $request->get('page', 1),
            'search' => $request->get('search', ''),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $this->getSortDirection($request),
        ];
    }

    /**
     * Obtenir le nombre d'éléments par page
     */
    protected function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', $this->getDefaultPerPage());
        
        // Limiter entre 10 et 100 éléments par page
        return max(10, min(100, $perPage));
    }

    /**
     * Nombre d'éléments par page par défaut
     */
    protected function getDefaultPerPage(): int
    {
        return 15;
    }

    /**
     * Obtenir la direction de tri
     */
    protected function getSortDirection(Request $request): string
    {
        $direction = strtolower($request->get('sort_direction', 'desc'));
        
        return in_array($direction, ['asc', 'desc']) ? $direction : 'desc';
    }

    /**
     * Appliquer la recherche sur une requête
     */
    protected function applySearch($query, string $searchTerm, array $searchableFields = [])
    {
        if (empty($searchTerm) || empty($searchableFields)) {
            return $query;
        }

        return $query->where(function($q) use ($searchTerm, $searchableFields) {
            foreach ($searchableFields as $field) {
                if (str_contains($field, '.')) {
                    // Recherche dans les relations
                    [$relation, $relationField] = explode('.', $field, 2);
                    $q->orWhereHas($relation, function($relQuery) use ($relationField, $searchTerm) {
                        $relQuery->where($relationField, 'like', "%{$searchTerm}%");
                    });
                } else {
                    // Recherche dans les champs directs
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            }
        });
    }

    /**
     * Appliquer le tri sur une requête
     */
    protected function applySorting($query, string $sortBy, string $sortDirection, array $allowedSortFields = [])
    {
        // Vérifier si le champ de tri est autorisé
        if (!empty($allowedSortFields) && !in_array($sortBy, $allowedSortFields)) {
            $sortBy = $allowedSortFields[0] ?? 'created_at';
        }

        if (str_contains($sortBy, '.')) {
            // Tri sur les relations
            [$relation, $relationField] = explode('.', $sortBy, 2);
            return $query->join(
                str_plural(strtolower($relation)) . ' as sort_' . $relation,
                $query->getModel()->getTable() . '.' . $relation . '_id',
                '=',
                'sort_' . $relation . '.id'
            )->orderBy('sort_' . $relation . '.' . $relationField, $sortDirection);
        }

        return $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Créer une pagination personnalisée
     */
    protected function createCustomPagination($items, int $total, int $perPage, int $currentPage, Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Formater les données de pagination pour les vues
     */
    protected function formatPaginationData($paginatedData, Request $request, array $additionalFilters = []): array
    {
        $filters = array_merge([
            'search' => $request->get('search', ''),
            'per_page' => $request->get('per_page', $this->getDefaultPerPage()),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
        ], $additionalFilters);

        return [
            'data' => $paginatedData,
            'filters' => $filters,
            'pagination_info' => [
                'total' => $paginatedData->total(),
                'per_page' => $paginatedData->perPage(),
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
            ]
        ];
    }

    /**
     * Options pour le sélecteur "éléments par page"
     */
    protected function getPerPageOptions(): array
    {
        return [
            10 => '10 par page',
            15 => '15 par page',
            25 => '25 par page',
            50 => '50 par page',
            100 => '100 par page',
        ];
    }

    /**
     * Créer des filtres de date prédéfinis
     */
    protected function getDateFilters(): array
    {
        return [
            'today' => [
                'label' => 'Aujourd\'hui',
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'yesterday' => [
                'label' => 'Hier',
                'start' => now()->subDay()->startOfDay(),
                'end' => now()->subDay()->endOfDay(),
            ],
            'this_week' => [
                'label' => 'Cette semaine',
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
            'last_week' => [
                'label' => 'Semaine dernière',
                'start' => now()->subWeek()->startOfWeek(),
                'end' => now()->subWeek()->endOfWeek(),
            ],
            'this_month' => [
                'label' => 'Ce mois',
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
            'last_month' => [
                'label' => 'Mois dernier',
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                'label' => 'Cette année',
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
        ];
    }
}