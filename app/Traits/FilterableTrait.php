<?php

namespace App\Traits;

trait FilterableTrait
{
    public function scopeFilter($query, \App\Filters\QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    // public function scopeFilter(\Illuminate\Database\Eloquent\Builder $builder, \App\Filters\QueryFilter $filters)
    // {
    //     return $filters->apply($builder);
    // }
}