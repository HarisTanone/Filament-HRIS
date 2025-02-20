<?php
namespace App\Filament\Resources\UserResource\Api\Handlers;

use App\Filament\Resources\UserResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = UserResource::class;

    public function handler(Request $request)
    {
        $id = $request->route('id');
        $query = static::getEloquentQuery();
        $user = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )->first();

        if (!$user) {
            return static::sendNotFoundResponse();
        }

        // Format respons manual yang menyerupai output transformer
        $formattedResponse = [
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'employee' => null
            ]
        ];

        if ($user->employee) {
            $officeData = null;
            if ($user->employee->office) {
                $officeData = [
                    'id' => $user->employee->office->id,
                    'office_name' => $user->employee->office->office_name,
                    'description' => $user->employee->office->description,
                ];
            }

            // Set manager_id dengan format yang diinginkan
            $managerData = null;
            if ($user->employee->manager_id && $user->employee->manager) {
                $managerData = [
                    'id' => $user->employee->manager->id,
                    'full_name' => $user->employee->manager->full_name,
                    'email' => $user->employee->manager->email,
                ];
            } else {
                $managerData = [
                    'id' => 0,
                    'full_name' => '',
                    'email' => '',
                ];
            }

            $formattedResponse['data']['employee'] = [
                'id' => $user->employee->id,
                'full_name' => $user->employee->full_name,
                'email' => $user->employee->email,
                'mobile_phone' => $user->employee->mobile_phone,
                'place_of_birth' => $user->employee->place_of_birth,
                'birthdate' => $user->employee->birthdate,
                'gender' => $user->employee->gender,
                'religion' => $user->employee->religion,
                'nik' => $user->employee->nik,
                'citizen_id_address' => $user->employee->citizen_id_address,
                'residential_address' => $user->employee->residential_address,
                'join_date' => $user->employee->join_date,
                'barcode' => $user->employee->barcode,
                'manager_id' => $managerData,
                'office_id' => $officeData,
                'photo' => $user->employee->photo,
            ];
        }

        return response()->json($formattedResponse);
    }
}