<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public $collects = \Spatie\Permission\Models\Permission::class;

    public function toArray($request)
    {
        $Array = [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'guard_name' => $this->guard_name,
        ];

        return $Array;
    }
}
