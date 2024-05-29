<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Task::class;

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
        }
        
        foreach ($toArrayResource as $key => $resource)
        {
            if( !collect(['id', 'steps', 'teams'])->merge($this->getFillable())->contains($key) )
            {
                $Array[$key] = $this->{$key};
            }
        }

        return collect($Array);
    }
}
