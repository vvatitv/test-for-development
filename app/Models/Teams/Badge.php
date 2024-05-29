<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Badge extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'teams_has_badges';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'options',
        'badge_id',
        'team_id',
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
            'teams_has_badges.name' => 10,
            'teams_has_badges.description' => 3,
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
        }while( Badge::where('slug', $uid)->first() instanceof Badge );
        
        return $uid;
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Badge', 'badge_id');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }

}
