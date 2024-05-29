<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\FilterableTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

class PassportTheme extends Model
{
    use HasFactory;
    use Sluggable;
    use FilterableTrait;
    use SearchableTrait;

	protected $table = 'teams_projects_passports_themes';

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
            'teams_projects_passports_themes.name' => 10,
            'teams_projects_passports_themes.description' => 3,
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
        }while( PassportTheme::where('slug', $uid)->first() instanceof PassportTheme );
        
        return $uid;
    }

    public function passports()
    {
        return $this->belongsToMany('App\Models\Teams\Passport', 'teams_projects_passport_has_themes', 'theme_id', 'passport_id');
    }
}
