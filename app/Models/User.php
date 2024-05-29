<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\ImageRepositoryTrait;
use App\Traits\Users\FieldTrait as UserFieldTrait;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Traits\FilterableTrait;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Lab404\Impersonate\Models\Impersonate;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;
use Shetabit\Visitor\Traits\Visitor;
use Shetabit\Visitor\Traits\Visitable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use Sluggable;
    use HasRoles;
    use UserFieldTrait;
    use ImageRepositoryTrait;
    use SearchableTrait;
    use FilterableTrait;
    use AuthenticationLoggable;
    use Impersonate;
    use HasRelationships;
    use HasTableAlias;
    use Visitor;
    use Visitable;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'slug',
        'email_verified_at',
        'api_token',
        'organization_id',
        'can_be_impersonated',
        'need_logout',
        'need_force_update',
        'unsubscription',
        'remember_token',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'password',
        'api_token',
        'organization_id',
        'need_logout',
        'need_force_update',
        'remember_token',
        'email_verified_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'need_logout' => 'boolean',
        'unsubscription' => 'boolean',
        'can_be_impersonated' => 'boolean',
    ];

    protected $dates = [];
    protected $appends = [
    ];

    protected $searchable = [
        'columns' => [
            'users.id' => 10,
            'users.email' => 8,
            'users_fields_has_users.value' => 10,
            'organizations.name' => 10,
            'organizations.abbreviation' => 8,
        ],
        'joins' => [
            'users_fields_has_users' => ['users.id', 'users_fields_has_users.user_id'],
            'organizations' => ['organizations.id', 'users.organization_id'],
        ],
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
        }while( User::where('slug', $uid)->first() instanceof User );
        
        return $uid;
    }

    public function canImpersonate()
    {
        return $this->hasRole('admin');
    }

    public function canBeImpersonated()
    {
        return $this->can_be_impersonated == 1;
    }

    public function getPicturesAttribute()
    {
        return $this->generateTreePictures();
    }

    public function getTeamAttribute()
    {
        if( $this->currentTeam->count() )
        {
            $team = $this->currentTeam->first();
        }
        else
        {
            $teams = $this->teams;

            if( !$teams->count() )
            {
                return null;
            }
            
            if( $teams->count() > 1 )
            {
                $team = $teams->sortByDesc('pivot.created_at')->first();
    
            }
            else
            {
                
                $team = $teams->first();
            }
        }

        return $team;
    }

    public function fields()
    {
        return $this->belongsToMany('App\Models\Users\Field', 'users_fields_has_users', 'user_id', 'field_id')
                    ->active()
                    ->using(\App\Pivots\Users\FieldPivot::class)
                    ->withPivot([
                        'value',
                        'points',
                        'is_show'
                    ]);
    }

    public function organization()
    {
        return $this->hasOne('App\Models\Organization', 'id', 'organization_id');
    }
    
    public function media()
    {
        return $this->morphMany('App\Models\Media', 'mediatable');
    }

    public function tests()
    {
        return $this->hasMany('App\Models\Users\Test', 'user_id', 'id');
    }

    public function teams()
    {
        return $this
                    ->belongsToMany('App\Models\Team', 'teams_has_members', 'user_id', 'team_id')
                    ->withTimestamps()
                    ->orderByPivot('created_at', 'desc');
    }

    public function currentTeam()
    {
        return $this
                    ->belongsToMany('App\Models\Team', 'users_has_currents_teams', 'user_id', 'team_id')
                    ->withTimestamps();
    }

    public function leadIn()
    {
        return $this->hasManyDeep(
            \App\Models\Team::class,
            ['teams_has_leads', User::class],
            [           
               'user_id',
               'id',
               'id'
            ],
            [          
              'id',
              'team_id',
              'id'
            ]
        );
    }

    public function mentorIn()
    {
        return $this->hasManyDeep(
            \App\Models\Team::class,
            ['teams_has_mentors', User::class],
            [           
               'user_id',
               'id',
               'id'
            ],
            [          
              'id',
              'team_id',
              'id'
            ]
        );
    }

    public function certifications()
    {
        return $this->hasMany('App\Models\Users\Certification', 'user_id', 'id');
    }

    public function badges()
    {
        return $this->hasMany('App\Models\Users\Badge', 'user_id', 'id');
    }

    public function instructions()
    {
        return $this
                    ->belongsToMany('App\Models\Instruction', 'users_has_instructions', 'user_id', 'instruction_id')
                    ->withTimestamps();
    }

    public function votes()
    {
        return $this->hasMany('App\Models\Users\Vote', 'user_id', 'id');
    }

    public function stars()
    {
        return $this->hasMany('App\Models\Users\Star', 'user_id', 'id');
    }

    public function quizzes()
    {
        return $this->hasMany(\App\Models\Users\Quiz::class, 'user_id', 'id');
    }

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new \App\Notifications\ResetPasswordNotification($token));
	}

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function getPicturesArrayAttribute()
    {
        return config('users.picture.sizes');
    }
}
