<?php

namespace App\Models\Quiz\Question;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'quizzes_questions_answers';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'text',
        'question_id',
        'options',
        'is_correct',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        'is_correct' => 'boolean',
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
        ];
    }

    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Answer::where('slug', $uid)->first() instanceof Answer );
        
        return $uid;
    }
    
    public function question()
    {
        return $this->belongsTo(\App\Models\Quiz\Question\Question::class, 'question_id', 'id');
    }

    public function users()
    {
        return $this
                    ->belongsToMany(\App\Models\User::class, 'users_quizzes_has_answers', 'aid', 'uid')
                    ->withPivot([
                        'quizid',
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
