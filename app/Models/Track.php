<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\FilterableTrait;

class Track extends Model
{
    use HasFactory;
    use Sluggable;
    use FilterableTrait;

	protected $table = 'tracks';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'options',
        'hash',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
    ];
    
    protected $dates = [];
    
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
        while( Track::where('slug', $uid)->first() instanceof Track );
        
        return $uid;
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'tracks_has_teams', 'track_id', 'team_id');
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Models\Task', 'tasks_has_tracks', 'track_id', 'task_id');
    }
}
