<?php

namespace App\Http\Resources\Step;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Step::class;
    public $settings;

    public function __construct($resource)
    {
        $this->settings = \App\Http\Resources\Setting\IndexResource::collection(\App\Models\Setting::get());
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        $toArrayResource = $this->resource->toArray();

        $is_current = $this->settings->where('slug', 'current-step')->first()->values == $this->id ? true : false;

        $Array = [
            'id' => $this->id,
            'is_current' => $is_current
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

        if( isset($toArrayResource['tasks']) )
        {
            $tasks = $this->tasks()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('tasks.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('tasks.'));
                    }
                }

                if( $withArray->count() )
                {
                    $tasks = $tasks->each->load($withArray->toArray());
                }
            }

            $Array['tasks'] = \App\Http\Resources\Task\IndexResource::collection($tasks);
        }

        if( isset($toArrayResource['votes']) )
        {
            $votes = $this->votes()->first();

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
                    $votes = $votes->load($withArray->toArray());
                }
            }

            $Array['votes'] = new \App\Http\Resources\Vote\IndexResource($votes);
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
            if( !collect(['id', 'is_current', 'tasks', 'votes', 'teams'])->merge($this->getFillable())->contains($key) )
            {
                $Array[$key] = $this->{$key};
            }
        }


        return collect($Array);
    }
}
