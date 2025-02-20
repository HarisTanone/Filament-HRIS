<?php
namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use App\Filament\Resources\AttendanceResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rupadana\ApiService\Http\Handlers;
use App\Models\Office;
use App\Models\Attendance;

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

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'office_id' => 'required|exists:offices,id',
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string'
        ]);


        $employeeId = $request->input('employee_id');
        $officeId = $request->input('office_id');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $today = Carbon::today();

        // Cek apakah user sudah melakukan clock-in hari ini
        $existingAttendance = Attendance::where('employee_id', $employeeId)
            ->where('office_id', $officeId)
            ->whereDate('created_at', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'message' => 'Anda sudah melakukan absensi clock-in hari ini.',
                'clock_in_time' => $existingAttendance->clock_in
            ], 400);
        }

        // Ambil data office berdasarkan ID
        $office = Office::find($officeId);
        if (!$office) {
            return response()->json(['message' => 'Office tidak ditemukan'], 404);
        }

        // Hitung jarak antara user dan kantor
        $distance = $this->calculateDistance($latitude, $longitude, $office->latitude, $office->longitude);

        // Cek apakah dalam radius
        $locationVerified = $distance <= $office->radius ? 1 : 0;

        // Proses foto absen
        $base64Image = $request->input('photo');
        $imageName = uniqid() . '.jpg';
        $filePath = "attendance-photos/{$imageName}";
        Storage::disk('public')->put($filePath, base64_decode($base64Image));

        // Simpan data absensi
        $attendance = new Attendance();
        $attendance->employee_id = $employeeId;
        $attendance->office_id = $officeId;
        $attendance->latitude = $latitude;
        $attendance->longitude = $longitude;
        $attendance->photo = $filePath;
        $attendance->clock_in = Carbon::now();
        $attendance->location_verified = $locationVerified;
        $attendance->attendance_notes = $attendance->generateAttendanceNotes();
        $attendance->save();

        return response()->json([
            'message' => 'Absensi berhasil disimpan',
            'data' => $attendance,
            'distance' => $distance,
            'location_verified' => $locationVerified
        ], 201);
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        // Konversi derajat ke radian
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance; // Jarak dalam meter
    }
}