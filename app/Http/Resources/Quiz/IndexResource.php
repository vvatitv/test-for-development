<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Quiz::class;

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

        if( isset($toArrayResource['media']) )
        {
            $Array['media'] = $this->media;
        }

        if( isset($toArrayResource['answers']) )
        {
            $answers = $this->answers()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('answers.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('answers.'));
                    }
                }

                if( $withArray->count() )
                {
                    $answers = $answers->each->load($withArray->toArray());
                }
            }

            $Array['answers'] = \App\Http\Resources\QuizAnswer\IndexResource::collection($answers);
        }

        if( isset($toArrayResource['users']) )
        {
            $users = $this->users()->get();

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('users.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('users.'));
                    }
                }

                if( $withArray->count() )
                {
                    $users = $users->each->load($withArray->toArray());
                }
            }

            $users = $users->each->withFields();

            $Array['users'] = \App\Http\Resources\User\IndexResource::collection($users);
        }

        if( isset($toArrayResource['theme']) )
        {
            $theme = $this->theme;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('theme.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('theme.'));
                    }
                }

                if( $withArray->count() )
                {
                    $theme = $theme->load($withArray->toArray());
                }
            }

            $Array['theme'] = new \App\Http\Resources\QuizTheme\IndexResource($theme);
        }

        return collect($Array);
    }
}
