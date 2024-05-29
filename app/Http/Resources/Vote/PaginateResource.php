<?php

namespace App\Http\Resources\Vote;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    public function toArray($request)
    {
        $toArrayResource = $this->resource->toArray();

        $this->resource->getCollection()->transform(function($item){
            return new \App\Http\Resources\Vote\IndexResource($item);
        });
        
        return parent::toArray($request);
    }
}
