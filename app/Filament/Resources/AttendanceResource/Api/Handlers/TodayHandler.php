<?php

namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use App\Filament\Resources\AttendanceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodayHandler extends Handlers
{
    public static string|null $uri = '/today/{employeeId}';
    public static string|null $resource = AttendanceResource::class;

    public function handler(Request $request)
    {
        $employeeId = $request->route('employeeId');
        $today = Carbon::today();

        $query = static::getEloquentQuery();
        $query = QueryBuilder::for(
            $query->where('employee_id', $employeeId)
                ->whereDate('clock_in', $today)
        )->first();

        if (!$query)
            return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
