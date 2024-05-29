<?php

namespace App\Http\Resources\IdeaTheme;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Teams\IdeaTheme::class;

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

        if( isset($toArrayResource['ideas']) )
        {
            $ideas = $this->ideas()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('ideas.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('ideas.'));
                    }
                }

                if( $withArray->count() )
                {
                    $ideas = $ideas->each->load($withArray->toArray());
                }
            }

            $Array['ideas'] = \App\Http\Resources\Idea\IndexResource::collection($ideas);
        }
        
        return collect($Array);
    }
}
