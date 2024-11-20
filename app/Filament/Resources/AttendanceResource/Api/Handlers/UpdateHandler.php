<?php
namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AttendanceResource;

class UpdateHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = AttendanceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $id = $request->route('id');
        $model = static::getModel()::find($id);

        if (!$model) {
            return static::sendNotFoundResponse("Attendance record not found");
        }

        if ($model->clock_out) {
            return response()->json(['message' => 'Clock-out has already been recorded'], 400);
        }

        $clockOut = $request->input('clock_out', Carbon::now());
        $latitudeOut = $request->input('latitude_out');
        $longitudeOut = $request->input('longitude_out');
        $base64Photo = $request->input('photo_out');

        if ($base64Photo) {
            $imageName = uniqid() . '.jpg';
            $filePath = "attendance-photos/{$imageName}";
            $decodedImage = base64_decode($base64Photo);
            Storage::disk('public')->put($filePath, $decodedImage);
            $model->photo_out = $filePath;
        }

        $model->clock_out = $clockOut;
        $model->latitude_out = $latitudeOut;
        $model->longitude_out = $longitudeOut;

        $model->attendance_notes = $model->generateAttendanceNotes();

        $model->save();

        return static::sendSuccessResponse($model, "Successfully updated clock-out");
    }
}
