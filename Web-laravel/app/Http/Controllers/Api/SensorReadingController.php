<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use App\Services\TelegramNotifier;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
