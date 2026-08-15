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
            'current' => 0.143,
            'power' => 29.5,
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
            'services.monitoring.warning_current_threshold' => 0.150,
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
            'current' => 0.150,
            'power' => 31.5,
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

    public function test_current_below_150_ma_does_not_trigger_warning_or_telegram(): void
    {
        config([
            'services.monitoring.warning_current_threshold' => 0.150,
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '123456',
            'services.telegram.alert_cooldown_seconds' => 0,
        ]);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/sensor-readings', [
            'device_id' => 'esp32-normal',
            'voltage' => 211.0,
            'current' => 0.143,
            'power' => 29.5,
            'energy' => 1.500,
            'frequency' => 50.0,
            'power_factor' => 0.98,
            'status' => 'normal',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'normal')
            ->assertJsonPath('telegram_sent', false);

        Http::assertNothingSent();
    }

    public function test_daily_chart_endpoint(): void
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = \Carbon\Carbon::now($tz);

        // Seed some readings for different hours of today
        \App\Models\SensorReading::create([
            'device_id' => 'esp32-test',
            'voltage' => 220.0,
            'current' => 1.0,
            'power' => 220.0,
            'energy' => 0.1,
            'frequency' => 50.0,
            'power_factor' => 0.9,
            'status' => 'normal',
            'recorded_at' => $now->copy()->hour(10)->minute(0)->second(0),
        ]);

        \App\Models\SensorReading::create([
            'device_id' => 'esp32-test',
            'voltage' => 230.0,
            'current' => 2.0,
            'power' => 460.0,
            'energy' => 0.2,
            'frequency' => 50.0,
            'power_factor' => 0.9,
            'status' => 'normal',
            'recorded_at' => $now->copy()->hour(12)->minute(0)->second(0),
        ]);

        $response = $this->getJson('/api/sensor-readings/daily-chart?date=' . $now->toDateString() . '&metric=power');

        $response
            ->assertOk()
            ->assertJsonPath('data.date', $now->toDateString())
            ->assertJsonPath('data.metric', 'power');

        $points = $response->json('data.points');
        $this->assertCount(24, $points);
        $this->assertEquals(220.0, $points[10]['value']);
        $this->assertEquals(460.0, $points[12]['value']);
        $this->assertNull($points[0]['value']);
    }
}
