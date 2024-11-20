<?php
namespace App\Filament\Resources\RoleResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return $this->resource->toArray();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "permissions" => $this->permissions->map(function ($permission) {
                return [
                    "id" => $permission->id,
                    "name" => $permission->name,
                ];
            }),
        ];
    }
}
