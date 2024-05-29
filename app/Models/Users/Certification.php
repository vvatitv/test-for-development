<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Certification extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'users_has_certifications';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'options',
        'cert_id',
        'user_id',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
    ];
    
    protected $dates = [
        'deleted_at'
    ];

    protected $searchable = [
        'columns' => [
            'users_has_certifications.name' => 10,
            'users_has_certifications.description' => 3,
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

    public function type()
    {
        return $this->belongsTo('App\Models\Certification', 'cert_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
