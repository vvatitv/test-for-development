<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theme extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'quizzes_themes';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'quizze_id',
        'name',
        'description',
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
            ]
        ];
    }

    public function getGenerateUniqueUidAttribute()
    {
        do{
            $uid = \Illuminate\Support\Str::uuid();
        }
        while( Theme::where('slug', $uid)->first() instanceof Theme );
        
        return $uid;
    }

    public function quizze()
    {
        return $this->belongsTo(\App\Models\Quiz\Quiz::class, 'quizze_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Quiz\Question\Question::class, 'theme_id');
    }

}
