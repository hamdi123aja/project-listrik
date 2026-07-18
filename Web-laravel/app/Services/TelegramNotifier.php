<?php

namespace App\Services;

use App\Models\SensorReading;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotifier
{
    public function sendCurrentWarning(SensorReading $reading, float $threshold): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            return false;
        }

        $cooldownSeconds = (int) config('services.telegram.alert_cooldown_seconds', 300);
        $cacheKey = 'telegram:current-warning:'.($reading->device_id ?: 'default');

        if ($cooldownSeconds > 0 && $this->isCoolingDown($cacheKey)) {
            return false;
        }

        $message = $this->buildCurrentWarningMessage($reading, $threshold);

        try {
            Http::timeout(8)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])->throw();

            if ($cooldownSeconds > 0) {
                Cache::put($cacheKey, true, $cooldownSeconds);
            }
            return true;
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim notifikasi Telegram arus tinggi.', [
                'reading_id' => $reading->id,
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }

    public function sendPowerWarning(SensorReading $reading, float $threshold): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            return false;
        }

        $cooldownSeconds = (int) config('services.telegram.alert_cooldown_seconds', 300);
        $cacheKey = 'telegram:power-warning:'.($reading->device_id ?: 'default');

        if ($cooldownSeconds > 0 && $this->isCoolingDown($cacheKey)) {
            return false;
        }

        $message = $this->buildPowerWarningMessage($reading, $threshold);

        try {
            Http::timeout(8)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])->throw();

            if ($cooldownSeconds > 0) {
                Cache::put($cacheKey, true, $cooldownSeconds);
            }
            return true;
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim notifikasi Telegram daya tinggi.', [
                'reading_id' => $reading->id,
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }

    private function isCoolingDown(string $cacheKey): bool
    {
        try {
            return Cache::has($cacheKey);
        } catch (\Throwable) {
            return false;
        }
    }

    private function buildCurrentWarningMessage(SensorReading $reading, float $threshold): string
    {
        $recordedAt = $reading->recorded_at
            ? $reading->recorded_at->timezone(config('app.timezone'))->format('d-m-Y H:i:s')
            : now(config('app.timezone'))->format('d-m-Y H:i:s');

        return implode("\n", [
            '<b>⚠️ WARNING ARUS TINGGI ⚠️</b>',
            // 'Terminal: <b>'.e($reading->device_id ?: 'Tidak diketahui').'</b>',
            'Arus: <b>'.number_format((float) $reading->current, 3, ',', '.').' A</b>',
            'Batas: '.number_format($threshold, 2, ',', '.').' A',
            'Daya: '.number_format((float) $reading->power, 2, ',', '.').' W',
            'Tegangan: '.number_format((float) $reading->voltage, 2, ',', '.').' V',
            'Waktu: '.$recordedAt,
            '',
            'Kurangi beban atau periksa terminal listrik.',
        ]);
    }

    private function buildPowerWarningMessage(SensorReading $reading, float $threshold): string
    {
        $recordedAt = $reading->recorded_at
            ? $reading->recorded_at->timezone(config('app.timezone'))->format('d-m-Y H:i:s')
            : now(config('app.timezone'))->format('d-m-Y H:i:s');

        return implode("\n", [
            '<b>⚠️ WARNING DAYA TINGGI ⚠️</b>',
            // 'Terminal: <b>'.e($reading->device_id ?: 'Tidak diketahui').'</b>',
            'Daya: <b>'.number_format((float) $reading->power, 2, ',', '.').' W</b>',
            'Batas: '.number_format($threshold, 2, ',', '.').' W',
            'Tegangan: '.number_format((float) $reading->voltage, 2, ',', '.').' V',
            'Arus: '.number_format((float) $reading->current, 3, ',', '.').' A',
            'Waktu: '.$recordedAt,
            '',
            'Mohon periksa beban listrik Anda.',
        ]);
    }
}
