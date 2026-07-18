<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:normal,warning,offline'],
            'device_id' => ['nullable', 'string', 'max:100'],
        ]);

        $query = SensorReading::query()->latest('recorded_at');

        if (!empty($validated['from'])) {
            $query->where('recorded_at', '>=', $validated['from'].' 00:00:00');
        }

        if (!empty($validated['to'])) {
            $query->where('recorded_at', '<=', $validated['to'].' 23:59:59');
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['device_id'])) {
            $query->where('device_id', $validated['device_id']);
        }

        $currentWarningThreshold = 8.0;
        $readings = $query->paginate(15)->withQueryString();
        $statsBase = (clone $query);
        $peakPower = (float) ($statsBase->max('power') ?? 0);
        $avgVoltage = (float) ($statsBase->avg('voltage') ?? 0);
        $totalEnergy = (float) ($statsBase->sum('energy') ?? 0);
        $deviceList = SensorReading::query()->select('device_id')->distinct()->pluck('device_id');

        return view('history.index', compact('readings', 'peakPower', 'avgVoltage', 'totalEnergy', 'deviceList', 'currentWarningThreshold'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = SensorReading::query()->latest('recorded_at');

        if ($request->filled('from')) {
            $query->where('recorded_at', '>=', $request->string('from').' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('recorded_at', '<=', $request->string('to').' 23:59:59');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->string('device_id'));
        }

        $filename = 'history_sensor_'.now(config('app.timezone'))->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['recorded_at', 'device_id', 'voltage', 'current', 'power', 'energy', 'frequency', 'power_factor', 'status']);

            $query->chunk(500, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->recorded_at?->timezone(config('app.timezone'))?->format('Y-m-d H:i:s'),
                        $row->device_id,
                        $row->voltage,
                        $row->current,
                        $row->power,
                        $row->energy,
                        $row->frequency,
                        $row->power_factor,
                        $row->status,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
