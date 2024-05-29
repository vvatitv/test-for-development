<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $table = 'users_votes';
    
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}
