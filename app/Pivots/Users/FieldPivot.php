<?php

namespace App\Pivots\Users;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FieldPivot extends Pivot
{
    protected $fillable = [
        'value',
        'is_show',
        'points'
    ];

    protected $casts = [
        'is_show' => 'integer',
        'points' => 'integer'
    ];

}