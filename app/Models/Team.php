<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ImageRepositoryTrait;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Traits\FilterableTrait;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Team extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use ImageRepositoryTrait;
    use SearchableTrait;
    use FilterableTrait;
    use HasRelationships;

	protected $table = 'teams';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'organization_id',
        'description',
        'values',
        'motto',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'organization_id',
    ];

    protected $casts = [
        'values' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
    ];
    
    protected $dates = [
        'deleted_at'
    ];
    
    protected $searchable = [
        'columns' => [
            'teams.name' => 10,
            'organizations.name' => 9,
            'organizations.abbreviation' => 8,
        ],
        'joins' => [
            'organizations' => ['teams.organization_id', 'organizations.id'],
        ],
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
        }while( Team::where('slug', $uid)->first() instanceof Team );
        
        return $uid;
    }

    public function getPicturesAttribute()
    {
        return $this->generateTreePictures();
    }

    public function getLeadAttribute()
    {
        return $this->leads()->first();
    }

    public function getMentorAttribute()
    {
        return $this->mentors()->first();
    }

    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }
    
    public function organization()
    {
        return $this->belongsTo('App\Models\Organization', 'organization_id');
    }

    public function badges()
    {
        return $this->hasMany('App\Models\Teams\Badge', 'team_id', 'id');
    }

    public function leads()
    {
        return $this->belongsToMany('App\Models\User', 'teams_has_leads', 'team_id', 'user_id');
    }

    public function mentors()
    {
        return $this->belongsToMany('App\Models\User', 'teams_has_mentors', 'team_id', 'user_id');
    }

    public function steps()
    {
        return $this
                    ->belongsToMany('App\Models\Step', 'steps_has_teams', 'team_id', 'step_id')
                    ->using(\App\Pivots\Teams\StepPivot::class)
                    ->withPivot([
                        'options'
                    ]);
    }

    public function idea()
    {
        return $this->hasOne(\App\Models\Teams\Idea::class, 'team_id');
        // return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_IDEA);
    }

    public function idea_votes()
    {
        return $this->hasManyDeepFromRelations($this->idea(), (new \App\Models\Teams\Idea())->votes());
    }

    public function idea_stars()
    {
        return $this->hasManyDeepFromRelations($this->idea(), (new \App\Models\Teams\Idea())->stars());
    }

    public function passport()
    {
        return $this->hasOne('App\Models\Teams\Passport', 'team_id');
        // return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_PASSPORT);
    }

    public function roadmap()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_ROADMAP);
    }

    public function riskmatrix()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_RISKMATRIX);
    }

    public function presentation()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_PRESENTATION);
    }

    public function teamtracktakesurvey()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY);
    }

    public function teamtrackselectioncasepart2()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2);
    }

    public function teamtrackpresentation()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_PRESENTATION);
    }

    public function teamtakequest()
    {
        return $this->hasOne('App\Models\Teams\Project', 'team_id')->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TAKE_QUEST);
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Teams\Project', 'team_id');
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Models\Task', 'teams_has_tasks', 'team_id', 'task_id')
                    ->using(\App\Pivots\Teams\TaskPivot::class)
                    ->withPivot(['options']);
    }

    public function members()
    {
        return $this->belongsToMany('App\Models\User', 'teams_has_members', 'team_id', 'user_id')->withTimestamps();
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Models\Track', 'tracks_has_teams', 'team_id', 'track_id');
    }

    public function tracksIdea()
    {
        return $this->hasOne('App\Models\Teams\Track\Idea', 'team_id');
    }

    public function briefcases()
    {
        return $this->belongsToMany(\App\Models\Briefcase::class, 'briefcase_has_teams', 'team_id', 'briefcase_id');
    }

    public function getPicturesArrayAttribute()
    {
        return config('teams.picture.sizes');
    }
}
