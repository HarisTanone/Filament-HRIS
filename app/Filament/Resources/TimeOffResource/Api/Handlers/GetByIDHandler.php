<?php

namespace App\Filament\Resources\TimeOffResource\Api\Handlers;

use App\Filament\Resources\TimeOffResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GetByIDHandler extends Handlers
{
    public static string|null $uri = '/employee/{employeeId}';
    public static string|null $resource = TimeOffResource::class;

    public function handler(Request $request)
    {
        $employeeId = $request->route('employeeId');
        $query = static::getEloquentQuery();
        $query = QueryBuilder::for(
            $query->where('employee_id', $employeeId)
        )->orderBy('created_at', 'desc')->get();

        if (!$query)
            return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
