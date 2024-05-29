<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'settings';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'description',
        'values',
        'slug',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        // 'values' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
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
        }while( Setting::where('slug', $uid)->first() instanceof Setting );
        
        return $uid;
    }

}
