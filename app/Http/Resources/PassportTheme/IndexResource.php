<?php

namespace App\Http\Resources\PassportTheme;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Teams\PassportTheme::class;

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

        if( isset($toArrayResource['passports']) )
        {
            $passports = $this->passports()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('passports.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('passports.'));
                    }
                }

                if( $withArray->count() )
                {
                    $passports = $passports->each->load($withArray->toArray());
                }
            }

            $Array['passports'] = \App\Http\Resources\Passport\IndexResource::collection($passports);
        }
        
        return collect($Array);
    }
}
