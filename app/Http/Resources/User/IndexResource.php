<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\User::class;

    public function toArray($request)
    {
        $toArrayResource = $this->resource->toArray();

        $Array = [
            'id' => $this->id
        ];

        if( !empty($this->getFillable()) )
        {
            foreach ($this->getFillable() as $key => $field)
            {
                if( !collect($this->getHidden())->contains($field) )
                {
                    $Array[$field] = $this->{$field};
                }
            }
        }

        if( isset($toArrayResource['fields']) && $this->fields->count() )
        {
            foreach ($this->fields as $key => $field)
            {
                if( empty($field->options) )
                {
                    $Array[$field->slug] = $field->pivot->value;
                }else{

                    if( !empty($field->options['relation']) )
                    {
                        $model = call_user_func($field->options['relation']['model'] . '::get');
                        $Array[$field->slug] = $model->where($field->options['relation']['identifier'], $field->pivot->value)->first();
                    }else{
                        $Array[$field->slug] = $field->pivot->value;
                    }
                }
            }
        }
        
        if( isset($toArrayResource['full_name']) )
        {
            $Array['full_name'] = $toArrayResource['full_name'];
        }

        if( isset($toArrayResource['full_name_short']) )
        {
            $Array['full_name_short'] = $toArrayResource['full_name_short'];
        }

        if( isset($toArrayResource['tests']) )
        {
            $tests = $this->tests()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('tests.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('tests.'));
                    }
                }

                if( $withArray->count() )
                {
                    $tests = $tests->each->load($withArray->toArray());
                }
            }

            $Array['tests'] = $tests;
        }

        if( isset($toArrayResource['certifications']) )
        {
            $certifications = $this->certifications()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('certifications.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('certifications.'));
                    }
                }

                if( $withArray->count() )
                {
                    $certifications = $certifications->each->load($withArray->toArray());
                }
            }

            $Array['certifications'] = $certifications;
        }

        if( isset($toArrayResource['badges']) )
        {
            $badges = $this->badges()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('badges.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('badges.'));
                    }
                }

                if( $withArray->count() )
                {
                    $badges = $badges->each->load($withArray->toArray());
                }
            }

            $Array['badges'] = $badges;
        }

        if( isset($toArrayResource['roles']) )
        {
            $roles = $this->roles()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('roles.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('roles.'));
                    }
                }

                if( $withArray->count() )
                {
                    $roles = $roles->each->load($withArray->toArray());
                }
            }

            $Array['roles'] = \App\Http\Resources\RoleResource::collection($roles);
        }

        if( isset($toArrayResource['permissions']) )
        {
            $permissions = $this->permissions()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('permissions.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('permissions.'));
                    }
                }

                if( $withArray->count() )
                {
                    $permissions = $permissions->each->load($withArray->toArray());
                }
            }

            $Array['permissions'] = \App\Http\Resources\PermissionResource::collection($permissions);
        }

        if( isset($toArrayResource['team']) )
        {
            $team = $this->team;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('team.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('team.'));
                    }
                }

                if( $withArray->count() )
                {
                    $team = $team->load($withArray->toArray());
                }
            }
            
            $Array['team'] = new \App\Http\Resources\Team\IndexResource($team);
        }

        if( isset($toArrayResource['stars']) )
        {
            $stars = $this->stars;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('stars.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('stars.'));
                    }
                }

                if( $withArray->count() )
                {
                    $stars = $stars->load($withArray->toArray());
                }
            }

            $Array['stars'] = \App\Http\Resources\Vote\IndexResource::collection($stars);
        }
        
        if( isset($toArrayResource['teams']) )
        {
            $teams = $this->teams()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('teams.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('teams.'));
                    }
                }

                if( $withArray->count() )
                {
                    $teams = $teams->each->load($withArray->toArray());
                }
            }

            $Array['teams'] = \App\Http\Resources\Team\IndexResource::collection($teams);

            if( $this->teams->count() )
            {
                $team = $this->team;

                if( $request->filled('with') && is_array($request->input('with')) )
                {
                    $withArray = collect([]);
    
                    foreach($request->input('with') as $key => $withElement)
                    {
                        if( Str::of($withElement)->contains('teams.') )
                        {
                            $withArray->push((string) Str::of($withElement)->afterLast('teams.'));
                        }
                    }
    
                    if( $withArray->count() )
                    {
                        $team = $team->load($withArray->toArray());
                    }
                }
                
                $Array['team'] = new \App\Http\Resources\Team\IndexResource($team);
            }
        }

        if( isset($toArrayResource['organization']) )
        {
            $organization = $this->organization;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('organization.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('organization.'));
                    }
                }

                if( $withArray->count() )
                {
                    $organization = $organization->load($withArray->toArray());
                }
            }

            $Array['organization'] = $organization;
        }

        if( isset($toArrayResource['votes']) )
        {
            $votes = $this->votes()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('votes.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('votes.'));
                    }
                }

                if( $withArray->count() )
                {
                    $votes = $votes->each->load($withArray->toArray());
                }
            }

            $Array['votes'] = \App\Http\Resources\Vote\IndexResource::collection($votes);
        }

        if( isset($toArrayResource['quizzes']) )
        {
            $quizzes = $this->quizzes()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('quizzes.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('quizzes.'));
                    }
                }

                if( $withArray->count() )
                {
                    $quizzes = $quizzes->each->load($withArray->toArray());
                }
            }

            $Array['quizzes'] = $quizzes;
        }

        $Array['pictures'] = $this->pictures;

        $Array['hasVerifiedEmail'] = !empty($this->email_verified_at) ? true : false;
        $Array['trashed'] = $this->trashed() ? true : false;
        $Array['instructions'] = $this->instructions;

        $Array['can_impersonate'] = $this->canImpersonate();
        
        $Array['imp'] = [];

        if( app('impersonate')->isImpersonating() )
        {
            $Array['imp']['user'] = app('impersonate')->findUserById(app('impersonate')->getImpersonatorId());
            $Array['imp']['logout'] = route('api.users.show.impersonate.leave', app('impersonate')->findUserById(app('impersonate')->getImpersonatorId()));
        }

        return collect($Array);
    }
}
