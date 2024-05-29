<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passport extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'teams_projects_passports';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'team_id',
        'status_id',
        'score',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'score' => 'decimal:2'
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
        }while( Passport::where('slug', $uid)->first() instanceof Passport );
        
        return $uid;
    }
    
    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }

    public function themes()
    {
        return $this->belongsToMany('App\Models\Teams\PassportTheme', 'teams_projects_passport_has_themes', 'passport_id', 'theme_id');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }
}
