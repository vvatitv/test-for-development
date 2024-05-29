<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class Quiz extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;
    use SearchableTrait;
    use FilterableTrait;

	protected $table = 'users_quizzes';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'status_id',
        'quizze_id',
        'user_id',
        'team_id',
        'step_id',
        'task_id',
        'params',
        'options',
        'slug',
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        'params' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
    ];
    
    protected $dates = [
        'deleted_at'
    ];

    protected $searchable = [
        'columns' => [
            'users_quizzes.name' => 9,
        ]
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'generate_unique_uid'
            ],
        ];
    }
    
    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Quiz::where('slug', $uid)->first() instanceof Quiz );
        
        return $uid;
    }

    public function quizze()
    {
        return $this->belongsTo(\App\Models\Quiz\Quiz::class, 'quizze_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class, 'team_id', 'id');
    }

    public function step()
    {
        return $this->belongsTo(\App\Models\Step::class, 'step_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(\App\Models\Task::class, 'task_id', 'id');
    }

    public function questions()
    {
        return $this
                    ->belongsToMany(\App\Models\Quiz\Question\Question::class, 'users_quizzes_has_questions', 'quizid', 'qid')
                    ->withPivot([
                        'uid',
                        'created_at',
                        'updated_at',
                    ])
                    ->withTimestamps();
    }

    public function answers()
    {
        return $this
                    ->belongsToMany(\App\Models\Quiz\Question\Answer::class, 'users_quizzes_has_answers', 'quizid', 'aid')
                    ->withPivot([
                        'uid',
                        'qid',
                        'point',
                        'time',
                        'text',
                        'created_at',
                        'updated_at',
                    ])
                    ->withTimestamps();
    }
}
