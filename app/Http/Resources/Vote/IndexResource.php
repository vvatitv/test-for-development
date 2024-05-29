<?php

namespace App\Http\Resources\Vote;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Vote::class;

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

        if( isset($toArrayResource['step']) )
        {
            $step = $this->step;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('step.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('step.'));
                    }
                }

                if( $withArray->count() )
                {
                    $step = $step->load($withArray->toArray());
                }
            }

            $Array['step'] = new \App\Http\Resources\Step\IndexResource($step);
        }

        return collect($Array);
    }
}
