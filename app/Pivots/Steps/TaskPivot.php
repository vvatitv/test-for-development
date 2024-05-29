<?php

namespace App\Pivots\Steps;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskPivot extends Pivot
{
    protected $fillable = [
        'ordering',
        'start_at',
        'end_at',
        'mod_at',
    ];

    protected $casts = [
        'ordering' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'mod_at' => 'datetime',
    ];

}