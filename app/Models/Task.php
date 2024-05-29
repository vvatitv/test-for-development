<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'tasks';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'options',
        'slug',
        'hash',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
    ];
    
    protected $dates = [
        'deleted_at'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'generate_unique_uid'
            ],
            'hash' => [
                'source' => 'generate_unique_uid'
            ],
        ];
    }

    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Task::where('slug', $uid)->first() instanceof Task );
        
        return $uid;
    }

    public function steps()
    {
        return $this
                    ->belongsToMany(\App\Models\Step::class, 'steps_has_tasks', 'task_id', 'step_id')
                    ->using(\App\Pivots\Steps\TaskPivot::class)
                    ->withPivot([
                        'ordering',
                        'start_at',
                        'end_at',
                        'mod_at',
                    ]);
    }

    public function teams()
    {
        return $this->belongsToMany(\App\Models\Team::class, 'teams_has_tasks', 'task_id', 'team_id')
                    ->using(\App\Pivots\Teams\TaskPivot::class)
                    ->withPivot(['options']);
    }

    public function tracks()
    {
        return $this->belongsToMany(\App\Models\Track::class, 'tasks_has_tracks', 'task_id', 'track_id');
    }

    public function briefcases()
    {
        return $this->belongsToMany(\App\Models\Briefcase::class, 'tasks_has_briefcase', 'task_id', 'briefcase_id');
    }
}
