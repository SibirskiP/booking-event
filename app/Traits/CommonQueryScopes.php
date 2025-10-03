<?php

namespace App\Traits;

trait CommonQueryScopes
{
    public function scopeFilterByDate($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('date', '>=', $from);
        }
        if ($to) {
            $query->where('date', '<=', $to);
        }
        return $query;
    }

    public function scopeSearchByTitle($query, $term = null)
    {
        if ($term) {
            $query->where('title', 'like', "%{$term}%");
        }
        return $query;
    }
}
