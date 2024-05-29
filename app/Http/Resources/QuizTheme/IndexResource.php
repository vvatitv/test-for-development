<?php

namespace App\Http\Resources\QuizTheme;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Quizzis\Theme::class;

    public function toArray($request)
    {
        $toArrayResource = $this->resource->toArray();

        $Array = [
            'id' => $this->id,
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

        if( isset($toArrayResource['questions']) )
        {
            $questions = $this->questions()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('questions.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('questions.'));
                    }
                }

                if( $withArray->count() )
                {
                    $questions = $questions->each->load($withArray->toArray());
                }
            }

            $Array['questions'] = \App\Http\Resources\Quiz\IndexResource::collection($questions);
        }

        return collect($Array);
    }
}
