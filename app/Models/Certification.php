<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Certification extends Model
{
    use HasFactory;
    use Sluggable;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'certifications';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'options',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
    ];
    
    protected $dates = [];

    protected $searchable = [
        'columns' => [
            'certifications.name' => 10,
            'certifications.description' => 3,
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
        }while( Certification::where('slug', $uid)->first() instanceof Certification );
        
        return $uid;
    }

    public function users()
    {
        return $this->hasMany('App\Models\Users\Certification', 'cert_id', 'id');
    }
}
