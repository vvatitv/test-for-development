<?php

namespace App\Http\Resources\Team;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Team::class;

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
        
        if( isset($toArrayResource['media']) )
        {
            $Array['media'] = $this->media;
        }
        
        if( isset($toArrayResource['tasks']) )
        {
            $Array['tasks'] = \App\Http\Resources\Task\IndexResource::collection($this->tasks);
        }
        
        if( isset($toArrayResource['idea']) )
        {
            $idea = $this->idea;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('idea.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('idea.'));
                    }
                }

                if( $withArray->count() )
                {
                    $idea = $idea->load($withArray->toArray());
                }
            }

            // $Array['idea'] = $idea;
            $Array['idea'] = new \App\Http\Resources\Idea\IndexResource($idea);
        }
        
        if( isset($toArrayResource['tracksIdea']) || isset($toArrayResource['tracks_idea']) )
        {
            $tracks_idea = $this->tracksIdea;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('tracksIdea.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('tracksIdea.'));
                    }
                }

                if( $withArray->count() )
                {
                    $tracks_idea = $tracks_idea->load($withArray->toArray());
                }
            }

            // $Array['idea'] = $idea;
            $Array['tracks_idea'] = new \App\Http\Resources\Idea\IndexResource($tracks_idea);
        }
        
        if( isset($toArrayResource['roadmap']) )
        {
            $roadmap = $this->roadmap;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('roadmap.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('roadmap.'));
                    }
                }

                if( $withArray->count() )
                {
                    $roadmap = $roadmap->load($withArray->toArray());
                }
            }

            $Array['roadmap'] = new \App\Http\Resources\Project\IndexResource($roadmap);
        }
        
        if( isset($toArrayResource['riskmatrix']) )
        {
            $riskmatrix = $this->riskmatrix;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('riskmatrix.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('riskmatrix.'));
                    }
                }

                if( $withArray->count() )
                {
                    $riskmatrix = $riskmatrix->load($withArray->toArray());
                }
            }

            $Array['riskmatrix'] = new \App\Http\Resources\Project\IndexResource($riskmatrix);
        }
        
        if( isset($toArrayResource['presentation']) )
        {
            $presentation = $this->presentation;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('presentation.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('presentation.'));
                    }
                }

                if( $withArray->count() )
                {
                    $presentation = $presentation->load($withArray->toArray());
                }
            }

            $Array['presentation'] = new \App\Http\Resources\Project\IndexResource($presentation);
        }
        
        if( isset($toArrayResource['passport']) )
        {
            $passport = $this->passport;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('passport.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('passport.'));
                    }
                }

                if( $withArray->count() )
                {
                    $passport = $passport->load($withArray->toArray());
                }
            }

            // $Array['passport'] = $passport;
            $Array['passport'] = new \App\Http\Resources\Passport\IndexResource($passport);
        }
        
        if( isset($toArrayResource['steps']) )
        {
            $steps = $this->steps()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('steps.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('steps.'));
                    }
                }

                if( $withArray->count() )
                {
                    $steps = $steps->each->load($withArray->toArray());
                }
            }
            
            $Array['steps'] = \App\Http\Resources\Step\IndexResource::collection($steps);
        }

        if( isset($toArrayResource['members']) )
        {
            $members = $this->members()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('members.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('members.'));
                    }
                }

                if( $withArray->count() )
                {
                    $members = $members->each->load($withArray->toArray());
                }
            }

            $members = $members->each->withFields();

            $Array['members'] = \App\Http\Resources\User\IndexResource::collection($members);
        }

        if( isset($toArrayResource['leads']) || isset($toArrayResource['lead']) )
        {

            $leads = $this->leads()->first();
            
            if( !empty($leads) )
            {
                $leads = $leads->withFields();

                if( $request->filled('with') && is_array($request->input('with')) )
                {
                    $withArray = collect([]);
    
                    foreach($request->input('with') as $key => $withElement)
                    {
                        if( Str::of($withElement)->contains('leads.') )
                        {
                            $withArray->push((string) Str::of($withElement)->afterLast('leads.'));
                        }
                    }
    
                    if( $withArray->count() )
                    {
                        $leads = $leads->load($withArray->toArray());
                    }
                }
    
                $Array['lead'] = new \App\Http\Resources\User\IndexResource($leads);
            }
        }

        if( isset($toArrayResource['mentors']) || isset($toArrayResource['mentor']) )
        {

            $mentors = $this->mentors()->first();
            
            if( !empty($mentors) )
            {
                $mentors = $mentors->withFields();

                if( $request->filled('with') && is_array($request->input('with')) )
                {
                    $withArray = collect([]);
    
                    foreach($request->input('with') as $key => $withElement)
                    {
                        if( Str::of($withElement)->contains('mentors.') )
                        {
                            $withArray->push((string) Str::of($withElement)->afterLast('mentors.'));
                        }
                    }
    
                    if( $withArray->count() )
                    {
                        $mentors = $mentors->load($withArray->toArray());
                    }
                }
    
                $Array['mentor'] = new \App\Http\Resources\User\IndexResource($mentors);
            }
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

        $Array['pictures'] = $this->pictures;

        foreach ($toArrayResource as $key => $resource)
        {
            if( !collect(['id', 'organization', 'media', 'idea', 'tracks_idea', 'badges', 'tasks', 'passport', 'steps', 'members', 'leads', 'lead', 'mentors', 'mentor', 'pictures'])->merge($this->getFillable())->contains($key) )
            {
                $Array[$key] = $this->{$key};
            }
        }

        return collect($Array);
    }
}
