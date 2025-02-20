<?php
namespace App\Filament\Resources\OvertimeResource\Api\Handlers;

use App\Filament\Resources\OvertimeResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = OvertimeResource::class;

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

            // Bersihkan spasi berlebih pada start_time dan end_time
            $startTimeRaw = preg_replace('/\s+/', '', $request->input('start_time'));
            $endTimeRaw = preg_replace('/\s+/', '', $request->input('end_time'));

            // Pastikan format waktu sesuai dengan 'H:i:s'
            if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $startTimeRaw) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $endTimeRaw)) {
                return response()->json(['error' => 'Format waktu tidak valid. Gunakan format HH:MM:SS'], 400);
            }

            // Konversi start_time dan end_time ke objek Carbon
            $startTime = Carbon::createFromFormat('H:i:s', $startTimeRaw);
            $endTime = Carbon::createFromFormat('H:i:s', $endTimeRaw);

            // Jika end_time lebih kecil dari start_time, tambah 1 hari (untuk kasus melewati tengah malam)
            if ($endTime < $startTime) {
                $endTime->addDay();
            }

            // Tentukan waktu mulai lembur berdasarkan hari
            $dayOfWeek = $startTime->dayOfWeek;
            $overtimeStart = null;

            if ($dayOfWeek >= Carbon::MONDAY && $dayOfWeek <= Carbon::FRIDAY) {
                $overtimeStart = Carbon::createFromTime(18, 30);
            } elseif ($dayOfWeek == Carbon::SATURDAY) {
                $overtimeStart = Carbon::createFromTime(13, 30);
            }

            // Pastikan overtimeStart tidak lebih besar dari endTime
            if ($overtimeStart && $endTime->greaterThan($overtimeStart)) {
                $startTime = max($startTime, $overtimeStart);
            }

            $totalHours = 0.00;
            if ($endTime->greaterThan($startTime)) {
                $totalMinutes = $endTime->diffInMinutes($startTime);
                $totalHours = round(abs($totalMinutes) / 60, 2);
            }

            // Set nilai ke model
            $model->fill($request->except(['total_hours', 'start_time', 'end_time']));
            $model->start_time = $startTime->format('H:i:s');
            $model->end_time = $endTime->format('H:i:s');
            $model->total_hours = max(0, $totalHours); // Pastikan tidak negatif

            $model->save();

            return static::sendSuccessResponse($model, "Successfully Created Resource");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}