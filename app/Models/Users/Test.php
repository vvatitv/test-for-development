<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $table = 'users_tests';
    
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'status_id',
        'user_id',
        'params',
        'options',
        'slug'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
    ];

    protected $casts = [
        'params' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
    ];

    protected $dates = [];
    protected $appends = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'generate_unique_uid'
            ]
        ];
    }

    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }while( Test::where('slug', $uid)->first() instanceof Test );
        
        return $uid;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}
