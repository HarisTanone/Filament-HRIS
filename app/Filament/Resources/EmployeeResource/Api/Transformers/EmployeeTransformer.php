<?php
namespace App\Filament\Resources\EmployeeResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $user = auth()->user();
        // return $this->resource->toArray();
        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "email" => $this->email,
            "mobile_phone" => $this->mobile_phone,
            "place_of_birth" => $this->place_of_birth,
            "birthdate" => $this->birthdate,
            "gender" => $this->gender,
            "religion" => $this->religion,
            "nik" => $this->nik,
            "citizen_id_address" => $this->citizen_id_address,
            "residential_address" => $this->residential_address,
            "join_date" => $this->join_date,
            "barcode" => $this->barcode,
            "user_id" => $this->user,
            "manager_id" => $this->manager,
            "office_id" => $this->office,
            "photo" => $this->photo,
        ];
    }
}
