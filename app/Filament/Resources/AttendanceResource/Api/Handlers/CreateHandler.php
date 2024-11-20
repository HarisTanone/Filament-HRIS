<?php
namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use App\Filament\Resources\AttendanceResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rupadana\ApiService\Http\Handlers;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = AttendanceResource::class;

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
        $employeeId = $request->input('employee_id');
        $officeId = $request->input('office_id');
        $today = Carbon::today();
        $model = new (static::getModel());

        $cek = $model->where('employee_id', $employeeId)
            ->where('office_id', $officeId)
            ->whereDate('created_at', $today)
            ->first();

        if ($cek) {
            return response()->json(['message' => 'You have already clocked in today'], 404);
        }

        $base64Image = $request->input('photo');

        if ($base64Image) {
            $imageName = uniqid() . '.jpg';
            $filePath = "attendance-photos/{$imageName}";
            $decodedImage = base64_decode($base64Image);
            Storage::disk('public')->put($filePath, $decodedImage);

            $model->photo = $filePath;
        }

        $model->fill($request->except('photo'));
        $model->clock_in = Carbon::now();
        $model->attendance_notes = $model->generateAttendanceNotes();

        $model->save();
        return static::sendSuccessResponse($model, "Successfully Created Resource");
    }
}