<?php

namespace App\Pivots\Teams;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskPivot extends Pivot
{
    protected $fillable = [
        'options'
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
    ];

}