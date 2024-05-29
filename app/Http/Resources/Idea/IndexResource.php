<?php

namespace App\Http\Resources\Idea;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Teams\Idea::class;

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

        if( isset($toArrayResource['media']) )
        {
            $Array['media'] = $this->media;
        }
        
        if( isset($toArrayResource['themes']) )
        {
            $themes = $this->themes()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('themes.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('themes.'));
                    }
                }

                if( $withArray->count() )
                {
                    $themes = $themes->each->load($withArray->toArray());
                }
            }

            $Array['themes'] = $themes;
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
        
        if( isset($toArrayResource['anonymousVotes']) || isset($toArrayResource['anonymous_votes']) )
        {
            $anonymousVotes = $this->anonymousVotes()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('anonymousVotes.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('anonymousVotes.'));
                    }
                }

                if( $withArray->count() )
                {
                    $anonymousVotes = $anonymousVotes->each->load($withArray->toArray());
                }
            }

            $Array['anonymous_votes'] = $anonymousVotes;
        }
        
        if( isset($toArrayResource['stars']) )
        {
            $stars = $this->stars()->get();

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
                    $stars = $stars->each->load($withArray->toArray());
                }
            }

            $Array['stars'] = \App\Http\Resources\Vote\IndexResource::collection($stars);
        }
        
        if( isset($toArrayResource['anonymousStars']) || isset($toArrayResource['anonymous_stars']) )
        {
            $anonymousStars = $this->anonymousStars()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('anonymousStars.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('anonymousStars.'));
                    }
                }

                if( $withArray->count() )
                {
                    $anonymousStars = $anonymousStars->each->load($withArray->toArray());
                }
            }

            $Array['anonymous_stars'] = $anonymousStars;
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

        return collect($Array);
    }
}
