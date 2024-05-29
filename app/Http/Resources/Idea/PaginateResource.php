<?php

namespace App\Http\Resources\Idea;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    public function toArray($request)
    {
        $toArrayResource = $this->resource->toArray();

        $this->resource->getCollection()->transform(function($item){
            return new \App\Http\Resources\Idea\IndexResource($item);
        });
        
        return parent::toArray($request);
    }
}
