<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Field extends Model
{
    use HasFactory;
    use Sluggable;

    protected $table = 'users_fields';
    
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'type_id',
        'options',
        'group_id',
        'is_active',
        'slug'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'group_id' => 'integer',
        'type_id' => 'integer',
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
        }while( Field::where('slug', $uid)->first() instanceof Field );
        
        return $uid;
    }

    public function scopeActive($query)
    {
    	return $query->where('is_active', 1);
    }

    public function users()
    {
    	return $this->belongsToMany('App\Models\User', 'users_fields_has_users', 'field_id', 'user_id')
                ->using(\App\Pivots\Users\FieldPivot::class)
                ->withPivot([
                    'value',
                    'points',
                    'is_show'
                ]);
    }
}
