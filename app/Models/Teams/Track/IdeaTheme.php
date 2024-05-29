<?php

namespace App\Models\Teams\Track;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class IdeaTheme extends Model
{
    use HasFactory;
    use Sluggable;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'teams_tracks_ideas_themes';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [];
    protected $dates = [];

    protected $searchable = [
        'columns' => [
            'teams_tracks_ideas_themes.name' => 10,
            'teams_tracks_ideas_themes.description' => 3,
        ]
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
        while( IdeaTheme::where('slug', $uid)->first() instanceof IdeaTheme );
        
        return $uid;
    }

    public function ideas()
    {
        return $this->belongsToMany(\App\Models\Teams\Track\Idea::class, 'teams_tracks_ideas_has_themes', 'theme_id', 'idea_id');
    }
}
