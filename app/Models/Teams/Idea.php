<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Idea extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'teams_projects_ideas';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'team_id',
        'status_id',
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
            'teams_projects_ideas.name' => 10,
            'teams_projects_ideas.description' => 3,
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
        }while( Idea::where('slug', $uid)->first() instanceof Idea );
        
        return $uid;
    }
    
    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }
    
    public function themes()
    {
        return $this->belongsToMany('App\Models\Teams\IdeaTheme', 'teams_projects_ideas_has_themes', 'idea_id', 'theme_id');
    }

    public function votes()
    {
        return $this->morphMany('App\Models\Users\Vote', 'taggable');
    }

    public function anonymousVotes()
    {
        return $this->morphMany(\App\Models\Anonymous\Vote::class, 'taggable');
    }

    public function stars()
    {
        return $this->morphMany('App\Models\Users\Star', 'taggable');
    }

    public function anonymousStars()
    {
        return $this->morphMany('App\Models\Anonymous\Star', 'taggable');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }
}
