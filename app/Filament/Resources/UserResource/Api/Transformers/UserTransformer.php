<?php
namespace App\Filament\Resources\UserResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Mengambil data employee berdasarkan user_id yang sama dengan id role
        $employee = $this->employee()->where('user_id', $this->id)->first();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "employee" => $employee ? [
                "id" => $employee->id,
                "full_name" => $employee->full_name,
                "email" => $employee->email,
                "mobile_phone" => $employee->mobile_phone,
                "place_of_birth" => $employee->place_of_birth,
                "birthdate" => $employee->birthdate,
                "gender" => $employee->gender,
                "religion" => $employee->religion,
                "nik" => $employee->nik,
                "citizen_id_address" => $employee->citizen_id_address,
                "residential_address" => $employee->residential_address,
                "join_date" => $employee->join_date,
                "barcode" => $employee->barcode,

                // Hanya menampilkan kolom tertentu dari manager_id
                "manager_id" => $employee->manager ? [
                    "id" => $employee->manager->id,
                    "full_name" => $employee->manager->full_name,
                    "email" => $employee->manager->email,
                ] : null,

                // Hanya menampilkan kolom tertentu dari office_id
                "office_id" => $employee->office ? [
                    "id" => $employee->office->id,
                    "office_name" => $employee->office->office_name,
                    "description" => $employee->office->description,
                ] : null,

                "photo" => $employee->photo,
            ] : null,
        ];
    }

}
