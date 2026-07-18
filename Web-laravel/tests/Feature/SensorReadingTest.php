<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SensorReadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensor_reading_can_be_stored_via_api(): void
    {
        $response = $this->postJson('/api/sensor-readings', [
            'device_id' => 'esp32-test',
            'voltage' => 220.4,
            'current' => 0.532,
            'power' => 117.2,
            'energy' => 1.254,
            'frequency' => 50.0,
            'power_factor' => 0.98,
            'status' => 'normal',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.device_id', 'esp32-test');

        $this->assertDatabaseHas('sensor_readings', [
            'device_id' => 'esp32-test',
            'status' => 'normal',
        ]);
    }

    public function test_high_current_triggers_warning_status_and_telegram_notification(): void
    {
        config([
            'services.monitoring.warning_current_threshold' => 15.0,
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '123456',
            'services.telegram.alert_cooldown_seconds' => 0,
        ]);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/sensor-readings', [
            'device_id' => 'esp32-warning',
            'voltage' => 220.0,
            'current' => 15.01,
            'power' => 3302.2,
            'energy' => 1.500,
            'frequency' => 50.0,
            'power_factor' => 0.91,
            'status' => 'normal',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'warning');

        $this->assertDatabaseHas('sensor_readings', [
            'device_id' => 'esp32-warning',
            'status' => 'warning',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bottest-token/sendMessage')
                && $request['chat_id'] === '123456'
                && str_contains($request['text'], 'WARNING ARUS TINGGI');
        });
    }
}
