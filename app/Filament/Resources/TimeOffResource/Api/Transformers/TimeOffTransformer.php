<?php
namespace App\Filament\Resources\TimeOffResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeOffTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
