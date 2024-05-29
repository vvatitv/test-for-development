<?php

namespace App\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public $collects = \App\Models\Setting::class;

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

        if( $this->isJson($this->values) )
        {
            $Array['values'] = json_decode($this->values, true);
        }

        return collect($Array);
    }

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
