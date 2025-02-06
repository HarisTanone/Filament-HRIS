<?php

namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use App\Filament\Resources\AttendanceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyHandler extends Handlers
{
    public static string|null $uri = '/monthly/{employeeId}/{month}';
    public static string|null $resource = AttendanceResource::class;

    public function handler(Request $request)
    {
        $employeeId = $request->route('employeeId');
        $month = $request->route('month');

        // Validasi format bulan (YYYY-MM)
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json([
                'message' => 'Invalid month format. Use YYYY-MM.',
            ], 400);
        }

        // Hitung tanggal awal dan akhir bulan
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // Query data absensi
        $query = static::getEloquentQuery();
        $attendances = $query->where('employee_id', $employeeId)
            ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
            ->get();

        if ($attendances->isEmpty()) {
            return static::sendNotFoundResponse();
        }

        // Inisialisasi data
        $data = [
            'absen' => [],
            'late_clock_in' => [],
            'early_clock_in' => [],
            'no_clock_in' => [],
            'no_clock_out' => [],
        ];

        // Default clock in time (contoh: 08:00)
        $defaultClockIn = Carbon::createFromTime(8, 0, 0); // Ganti dengan `app.default_clock_in` jika ada

        // Loop melalui setiap absensi
        foreach ($attendances as $attendance) {
            $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in) : null;
            $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;

            // Cek no clock in
            if (!$clockIn && $clockOut) {
                $data['no_clock_in'][] = $attendance;
                continue;
            }

            // Cek no clock out
            if ($clockIn && !$clockOut) {
                $data['no_clock_out'][] = $attendance;
                continue;
            }

            // Cek late clock in
            if ($clockIn && $clockIn->gt($defaultClockIn)) {
                $data['late_clock_in'][] = $attendance;
            }

            // Cek early clock in (30 menit sebelum default clock in)
            $earlyThreshold = $defaultClockIn->copy()->subMinutes(30);
            if ($clockIn && $clockIn->lt($earlyThreshold)) {
                $data['early_clock_in'][] = $attendance;
            }

            // Absen yang valid
            if ($clockIn && $clockOut) {
                $data['absen'][] = $attendance;
            }
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'employee_id' => $employeeId,
                'month' => $month,
                'total_attendances' => $attendances->count(),
                'total_absen' => count($data['absen']),
                'total_late_clock_in' => count($data['late_clock_in']),
                'total_early_clock_in' => count($data['early_clock_in']),
                'total_no_clock_in' => count($data['no_clock_in']),
                'total_no_clock_out' => count($data['no_clock_out']),
            ],
        ]);
    }
}