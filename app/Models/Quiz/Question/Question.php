<?php

namespace App\Models\Quiz\Question;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'quizzes_questions';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'quizze_id',
        'text',
        'theme_id',
        'answer_type_id',
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
        while( Question::where('slug', $uid)->first() instanceof Question );
        
        return $uid;
    }
    
    public function quizze()
    {
        return $this->belongsTo(\App\Models\Quiz\Quiz::class, 'quizze_id', 'id');
    }
    
    public function theme()
    {
        return $this->belongsTo(\App\Models\Quiz\Theme::class, 'theme_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(\App\Models\Quiz\Question\Answer::class, 'question_id');
    }
}

