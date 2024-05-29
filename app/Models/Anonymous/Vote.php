<?php

namespace App\Models\Anonymous;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $table = 'users_anonymous_votes';
    
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'user_session',
        'taggable_id',
        'taggable_type',
        'options'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
    ];

    protected $dates = [];
    protected $appends = [];

    public function taggable()
    {
        return $this->morphTo();
    }
}
