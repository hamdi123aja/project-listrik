<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use App\Services\TelegramNotifier;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SensorReadingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => SensorReading::latest('recorded_at')->paginate(50),
        ]);
    }

    public function latest(): JsonResponse
    {
        return response()->json([
            'data' => SensorReading::latest('recorded_at')->first(),
        ]);
    }

    public function dailyChart(Request $request): JsonResponse
    {
        $allowedMetrics = ['power', 'current', 'voltage', 'energy', 'frequency', 'power_factor'];

        $validated = $request->validate([
            'date'   => ['nullable', 'date_format:Y-m-d'],
            'metric' => ['nullable', 'string', 'in:' . implode(',', $allowedMetrics)],
        ]);

        $tz     = config('app.timezone', 'Asia/Jakarta');
        $date   = $validated['date'] ?? Carbon::now($tz)->toDateString();
        $metric = $validated['metric'] ?? 'power';

        // Build the "from" and "to" timestamps in UTC for the query
        $from = Carbon::parse($date, $tz)->startOfDay()->utc();
        $to   = Carbon::parse($date, $tz)->endOfDay()->utc();

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: use strftime with UTC recorded_at
            $rows = SensorReading::query()
                ->selectRaw("CAST(strftime('%H', recorded_at) AS INTEGER) as hour, AVG({$metric}) as avg_value")
                ->whereBetween('recorded_at', [$from, $to])
                ->groupByRaw("strftime('%H', recorded_at)")
                ->orderByRaw("strftime('%H', recorded_at)")
                ->get();
        } else {
            // MySQL / MariaDB: HOUR() works natively
            $rows = SensorReading::query()
                ->selectRaw("HOUR(recorded_at) as hour, AVG({$metric}) as avg_value")
                ->whereBetween('recorded_at', [$from, $to])
                ->groupByRaw('HOUR(recorded_at)')
                ->orderByRaw('HOUR(recorded_at)')
                ->get()
                ->map(function ($row) {
                    $row->hour = (int) $row->hour;
                    return $row;
                });
        }

        // Build a full 24-hour array — hours without data get null
        $hourlyMap = $rows->keyBy('hour');
        $points    = [];
        for ($h = 0; $h < 24; $h++) {
            $points[] = [
                'label' => sprintf('%02d:00', $h),
                'value' => isset($hourlyMap[$h]) ? round((float) $hourlyMap[$h]->avg_value, 3) : null,
            ];
        }

        return response()->json([
            'data' => [
                'date'   => $date,
                'metric' => $metric,
                'points' => $points,
            ],
        ]);
    }

    public function store(Request $request, TelegramNotifier $telegramNotifier): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['nullable', 'string', 'max:100'],
            'voltage' => ['required', 'numeric'],
            'current' => ['required', 'numeric'],
            'power' => ['required', 'numeric'],
            'energy' => ['required', 'numeric'],
            'frequency' => ['nullable', 'numeric'],
            'power_factor' => ['nullable', 'numeric', 'between:0,1'],
            'status' => ['nullable', 'string', Rule::in(['normal', 'warning', 'offline'])],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $expectedApiKey = env('SENSOR_API_KEY');
        $authHeader = $request->bearerToken();
        $apiHeader = $request->header('X-API-KEY');

        if ($expectedApiKey && $apiHeader !== $expectedApiKey && $authHeader !== $expectedApiKey) {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        $warningCurrentThreshold = (float) config('services.monitoring.warning_current_threshold', 15.0);
        $warningPowerThreshold = (float) config('services.monitoring.warning_power_threshold', 15.0);
        
        $current = (float) $validated['current'];
        $power = (float) $validated['power'];

        $isWarning = $current > $warningCurrentThreshold || $power > $warningPowerThreshold;
        $status = $isWarning
            ? 'warning'
            : ($validated['status'] ?? 'normal');

        $reading = SensorReading::create([
            ...$validated,
            'status' => $status,
            'recorded_at' => $validated['recorded_at'] ?? CarbonImmutable::now(config('app.timezone')),
            'raw_payload' => $request->all(),
        ]);

        $telegramSent = false;

        if ($power > $warningPowerThreshold) {
            if ($telegramNotifier->sendPowerWarning($reading, $warningPowerThreshold)) {
                $telegramSent = true;
            }
        }

        if ($current > $warningCurrentThreshold) {
            if ($telegramNotifier->sendCurrentWarning($reading, $warningCurrentThreshold)) {
                $telegramSent = true;
            }
        }

        return response()->json([
            'message' => 'Data sensor berhasil disimpan.',
            'data' => $reading,
            'telegram_sent' => $telegramSent,
        ], 201);
    }
}
