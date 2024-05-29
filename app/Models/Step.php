<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use App\Traits\FilterableTrait;

class Step extends Model
{
    use HasFactory;
    use Sluggable;
    use FilterableTrait;
    use HasRelationships;

	protected $table = 'steps';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'start_at',
        'end_at',
        'options',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
    ];

    protected $dates = [
        'start_at',
        'end_at',
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
        while( Step::where('slug', $uid)->first() instanceof Step );
        
        return $uid;
    }

    public function teams()
    {
        return $this
                    ->belongsToMany('App\Models\Team', 'steps_has_teams', 'step_id', 'team_id')
                    ->using(\App\Pivots\Teams\StepPivot::class)
                    ->withPivot([
                        'options'
                    ]);
    }

    public function teamsreals()
    {
        return $this
                    ->teams()
                    ->wherePivotNull('options')
                    ->orWherePivot('options', ['tasks-access' => true]);
    }

    public function teams_members()
    {
        return $this->hasManyDeepFromRelations($this->teams(), (new \App\Models\Team())->members());
    }
    
    public function teamsreals_members()
    {
        return $this->hasManyDeepFromRelations($this->teamsreals(), (new \App\Models\Team())->members());
    }

    public function votes()
    {
        return $this->hasOne('App\Models\Vote', 'step_id', 'id');
    }

    public function tasks()
    {
        return $this
                    ->belongsToMany('App\Models\Task', 'steps_has_tasks', 'step_id', 'task_id')
                    ->using(\App\Pivots\Steps\TaskPivot::class)
                    ->withPivot([
                        'ordering',
                        'start_at',
                        'end_at',
                        'mod_at',
                    ]);
    }
}
