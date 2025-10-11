<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

trait OptimizedPagination
{
    /**
     * Pagination optimisée avec cache
     */
    protected function paginateWithCache(
        Builder $query,
        int $perPage = 15,
        string $cacheKey = null,
        int $cacheTtl = 300
    ): LengthAwarePaginator {
        
        $page = request('page', 1);
        
        if ($cacheKey) {
            $fullCacheKey = $cacheKey . '_page_' . $page . '_per_' . $perPage;
            
            return Cache::remember($fullCacheKey, $cacheTtl, function() use ($query, $perPage) {
                return $query->paginate($perPage);
            });
        }
        
        return $query->paginate($perPage);
    }
    
    /**
     * Simple pagination (sans count total) pour de meilleures performances
     */
    protected function simplePaginate(Builder $query, int $perPage = 15)
    {
        return $query->simplePaginate($perPage);
    }
    
    /**
     * Cursor pagination pour de très grandes datasets
     */
    protected function cursorPaginate(Builder $query, int $perPage = 15, string $column = 'id')
    {
        return $query->cursorPaginate($perPage, ['*'], 'cursor', null, $column);
    }
    
    /**
     * Pagination avec statistiques mises en cache
     */
    protected function paginateWithStats(
        Builder $query, 
        int $perPage = 15, 
        string $statsCacheKey = null
    ) {
        $data = $query->paginate($perPage);
        
        if ($statsCacheKey) {
            $stats = Cache::remember($statsCacheKey . '_stats', 300, function() use ($query) {
                return [
                    'total' => $query->count(),
                    'today' => $query->whereDate('created_at', today())->count(),
                    'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                ];
            });
            
            $data->appends(['stats' => $stats]);
        }
        
        return $data;
    }
}