<?php

namespace App\Http\Resources\QuizAnswer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Quizzis\Answer::class;

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

        if( isset($toArrayResource['question']) )
        {
            $question = $this->question;

            if( $request->filled('with') && is_array($request->input('with')) )
            {
                $withArray = collect([]);

                foreach($request->input('with') as $key => $withElement)
                {
                    if( Str::of($withElement)->contains('question.') )
                    {
                        $withArray->push((string) Str::of($withElement)->afterLast('question.'));
                    }
                }

                if( $withArray->count() )
                {
                    $question = $question->load($withArray->toArray());
                }
            }

            $Array['question'] = new \App\Http\Resources\Quiz\IndexResource($question);
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

        if( isset($toArrayResource['pivot']) )
        {
            $Array['time'] = $this->pivot->time;
            $Array['point'] = $this->pivot->point;
            $Array['text'] = $this->pivot->text;
        }

        return collect($Array);
    }
}
