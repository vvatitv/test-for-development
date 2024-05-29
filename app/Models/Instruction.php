<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instruction extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

	protected $table = 'instructions';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
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
        'options' => \Illuminate\Database\Eloquent\Casts\AsCollection::class
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
        }while( Instruction::where('slug', $uid)->first() instanceof Instruction );
        
        return $uid;
    }

    public function users()
    {
        return $this
                    ->belongsToMany('App\Models\User', 'users_has_instructions', 'instruction_id', 'user_id')
                    ->withTimestamps();
    }
}
