<?php

namespace App\Models\Quiz;

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

	protected $table = 'quizzes';

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

    protected $searchable = [
        'columns' => [
            'quizzes.hash' => 10,
            'quizzes.name' => 9,
        ]
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'generate_unique_uid'
            ],
            'hash' => [
                'source' => 'generate_unique_hash'
            ],
        ];
    }

    public function getGenerateUniqueHashAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Quiz::where('hash', $uid)->first() instanceof Quiz );
        
        return $uid;
    }

    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Quiz::where('slug', $uid)->first() instanceof Quiz );
        
        return $uid;
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Quiz\Question\Question::class, 'quizze_id');
    }

    public function themes()
    {
        return $this->hasMany(\App\Models\Quiz\Theme::class, 'quizze_id');
    }

    public function usersQuizzes()
    {
        return $this->hasMany(\App\Models\Users\Quiz::class, 'quizze_id');
    }
}
