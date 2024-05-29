<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'teams_projects';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'type_id',
        'description',
        'team_id',
        'status_id',
        'score',
        'options',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        'score' => 'decimal:2',
        'type_id' => 'integer',
    ];
    
    protected $dates = [
        'deleted_at'
    ];
    
    protected $appends = [
        'type'
    ];

    const CONST_TYPE_IDEA = 100;
    const CONST_TYPE_PASSPORT = 200;
    const CONST_TYPE_ROADMAP = 300;
    const CONST_TYPE_RISKMATRIX = 400;
    const CONST_TYPE_PRESENTATION = 500;

    const CONST_TYPE_TEAM_TRACK_TAKE_SURVEY = 600;
    const CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2 = 700;
    const CONST_TYPE_TEAM_TAKE_QUEST = 800;
    const CONST_TYPE_TEAM_TRACK_PRESENTATION = 900;

	protected $typeLabels = [
        self::CONST_TYPE_IDEA => 'idea',
        self::CONST_TYPE_PASSPORT => 'passport',
        self::CONST_TYPE_ROADMAP => 'roadmap',
        self::CONST_TYPE_RISKMATRIX => 'risk-matrix',
        self::CONST_TYPE_PRESENTATION => 'presentation',
        self::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY => 'team-track-take-survey',
        self::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2 => 'team-track-selection-case-part-2',
        self::CONST_TYPE_TEAM_TAKE_QUEST => 'team-take-quest',
        self::CONST_TYPE_TEAM_TRACK_PRESENTATION => 'team-track-presentation',
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
        }
        while( Project::where('slug', $uid)->first() instanceof Project );
        
        return $uid;
    }
    
    public function getTypeAttribute()
    {
        return $this->typeLabels[$this->attributes['type_id']];
    }

    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }
}
