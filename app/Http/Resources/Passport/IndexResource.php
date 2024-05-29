<?php

namespace App\Http\Resources\Passport;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Teams\Passport::class;

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
