<?php
namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AttendanceResource;
use App\Models\Office;
use App\Models\Attendance;
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

        // Pastikan Clock In sudah dilakukan
        if (!$model->clock_in) {
            return response()->json(['message' => 'Clock-in record not found, you must clock in first'], 400);
        }

        // Pastikan Clock Out belum dilakukan
        if ($model->clock_out) {
            return response()->json(['message' => 'Clock-out has already been recorded'], 400);
        }

        // Ambil koordinat Clock Out dari request
        $clockOut = Carbon::now();
        $latitudeOut = $request->input('latitude_out');
        $longitudeOut = $request->input('longitude_out');
        $base64Photo = $request->input('photo_out');

        // Simpan foto jika ada
        if ($base64Photo) {
            $imageName = uniqid() . '.jpg';
            $filePath = "attendance-photos/{$imageName}";
            $decodedImage = base64_decode($base64Photo);
            Storage::disk('public')->put($filePath, $decodedImage);
            $model->photo_out = $filePath;
        }

        // Cek apakah Clock Out berada dalam radius kantor
        $office = Office::find($model->office_id);
        $locationVerifiedClockOut = 0;
        if ($office) {
            $distance = $this->haversineDistance(
                $office->latitude,
                $office->longitude,
                $latitudeOut,
                $longitudeOut
            );

            if ($distance <= $office->radius) {
                $locationVerifiedClockOut = 1;
            }
        }

        // Update data Clock Out
        $model->clock_out = $clockOut;
        $model->latitude_out = $latitudeOut;
        $model->longitude_out = $longitudeOut;
        $model->location_verified_clockOut = $locationVerifiedClockOut;

        $model->save();

        return static::sendSuccessResponse($model, "Successfully updated clock-out");
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Jarak dalam meter
    }
}
