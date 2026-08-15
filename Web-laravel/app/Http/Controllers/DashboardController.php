<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): View
    {
        $warningCurrentThreshold = (float) config('services.monitoring.warning_current_threshold', 0.150);

        if (!Schema::hasTable('sensor_readings')) {
            return view('dashboard.index', [
                'latest' => null,
                'history' => collect(),
                'displayedPower' => 0,
                'displayedEnergy' => 0,
                'estimatedCost' => 0,
                'tariffPerKwh' => 1444.7,
                'warningCurrentThreshold' => $warningCurrentThreshold,
            ]);
        }

        $latest = SensorReading::latest('recorded_at')->first();
        $history = SensorReading::latest('recorded_at')->limit(10)->get();

        $displayedPower = $latest ? (float) $latest->power : 0;
        $displayedEnergy = $latest ? (float) $latest->energy : 0;

        $tariffPerKwh = 1444.7;
        $estimatedCost = $displayedEnergy * $tariffPerKwh;

        if ($latest && $history->isEmpty()) {
            $history = collect([$latest]);
        }

        return view('dashboard.index', [
            'latest' => $latest,
            'history' => $history,
            'displayedPower' => $displayedPower,
            'displayedEnergy' => $displayedEnergy,
            'estimatedCost' => $estimatedCost,
            'tariffPerKwh' => $tariffPerKwh,
            'warningCurrentThreshold' => $warningCurrentThreshold,
        ]);
    }
}
