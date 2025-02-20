<?php
namespace App\Filament\Resources\TimeOffResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TimeOffResource;
use Illuminate\Support\Facades\Storage;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = TimeOffResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        try {
            $model = new (static::getModel());
            $data = $request->all();

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $request->validate([
                    'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Sesuaikan sesuai dengan kebutuhan
                ]);

                $filePath = $file->store('documents', 'public'); // Menyimpan file dalam folder `documents` pada storage `public`
                $data['document'] = $filePath;
            }
            $model->fill($data);
            // dd($data);
            $model->save();
            return static::sendSuccessResponse($model, "Successfully Create Resource");
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
