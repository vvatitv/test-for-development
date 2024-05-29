<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Organization extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use SearchableTrait;
    use FilterableTrait;

	protected $table = 'organizations';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [];
    
    protected $dates = [
        'deleted_at'
    ];

    protected $searchable = [
        'columns' => [
            'organizations.name' => 10,
            'abbreviation.name' => 10,
        ]
    ];

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
        }while( Organization::where('slug', $uid)->first() instanceof Organization );
        
        return $uid;
    }

    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }
    
    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'organization_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'organization_id', 'id');
    }
}
