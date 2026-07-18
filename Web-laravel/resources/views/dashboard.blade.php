<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Listrik Rumah</title>
    <meta http-equiv="refresh" content="10">
    <style>
        :root {
            --bg: #f4efe7;
            --panel: #fffdf8;
            --ink: #1f2937;
            --muted: #6b7280;
            --accent: #0f766e;
            --accent-soft: #ccfbf1;
            --line: #e5ddd2;
            --warn: #c2410c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, #fde68a 0, transparent 30%),
                linear-gradient(135deg, #f4efe7 0%, #e7f6f2 100%);
            color: var(--ink);
        }

        .container {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0 48px;
        }

        .hero {
            background: rgba(255, 253, 248, 0.85);
            border: 1px solid rgba(229, 221, 210, 0.9);
            border-radius: 28px;
            padding: 28px;
            backdrop-filter: blur(10px);
            box-shadow: 0 16px 50px rgba(15, 23, 42, 0.08);
        }

        h1, h2 {
            margin: 0;
        }

        .subtitle {
            margin-top: 10px;
            color: var(--muted);
            line-height: 1.6;
            max-width: 760px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .value {
            margin-top: 10px;
            font-size: 32px;
            font-weight: 700;
        }

        .meta {
            margin-top: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            padding: 10px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 14px;
            font-weight: 700;
        }

        .table-wrap {
            margin-top: 24px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--panel);
            border-radius: 22px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8f5ef;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty {
            margin-top: 24px;
            padding: 24px;
            border: 1px dashed var(--line);
            border-radius: 20px;
            background: rgba(255, 253, 248, 0.8);
            color: var(--muted);
        }

        .footer-note {
            margin-top: 20px;
            color: var(--warn);
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .container {
                width: min(100% - 20px, 1120px);
                padding-top: 20px;
            }

            .hero, .card {
                border-radius: 18px;
            }

            .value {
                font-size: 26px;
            }

            th, td {
                padding: 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="hero">
            <h1>Dashboard Monitoring Konsumsi Listrik</h1>
            <p class="subtitle">
                Data dibaca dari sensor PZEM-004T melalui ESP32, dikirim ke Laravel API, lalu disimpan ke database dan ditampilkan otomatis di halaman ini.
            </p>

            @if ($latest)
                <div class="status">
                    Status: {{ strtoupper($latest->status) }} | Device: {{ $latest->device_id ?: 'esp32-pzem' }}
                </div>

                <div class="grid">
                    <article class="card">
                        <div class="label">Tegangan</div>
                        <div class="value">{{ number_format($latest->voltage, 2) }} V</div>
                        <div class="meta">Pembaruan: {{ $latest->recorded_at?->timezone(config('app.timezone'))?->format('d-m-Y H:i:s') }}</div>
                    </article>
                    <article class="card">
                        <div class="label">Arus</div>
                        <div class="value">{{ number_format($latest->current, 3) }} A</div>
                        <div class="meta">Faktor daya: {{ number_format($latest->power_factor ?? 0, 2) }}</div>
                    </article>
                    <article class="card">
                        <div class="label">Daya</div>
                        <div class="value">{{ number_format($latest->power, 2) }} W</div>
                        <div class="meta">Rata-rata hari ini: {{ number_format($todayPower ?? 0, 2) }} W</div>
                    </article>
                    <article class="card">
                        <div class="label">Energi</div>
                        <div class="value">{{ number_format($latest->energy, 3) }} kWh</div>
                        <div class="meta">Maksimum hari ini: {{ number_format($todayEnergy ?? 0, 3) }} kWh</div>
                    </article>
                    <article class="card">
                        <div class="label">Frekuensi</div>
                        <div class="value">{{ number_format($latest->frequency ?? 0, 2) }} Hz</div>
                        <div class="meta">Endpoint API: /api/sensor-readings</div>
                    </article>
                </div>
            @else
                <div class="empty">
                    Belum ada data sensor yang masuk. Jalankan Laravel, isi URL API di ESP32, lalu kirim data pertama dari perangkat.
                </div>
            @endif
        </section>

        <section class="table-wrap">
            <h2>Riwayat Data Terakhir</h2>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tegangan</th>
                        <th>Arus</th>
                        <th>Daya</th>
                        <th>Energi</th>
                        <th>Frekuensi</th>
                        <th>PF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $item)
                        <tr>
                            <td>{{ $item->recorded_at?->timezone(config('app.timezone'))?->format('d-m-Y H:i:s') }}</td>
                            <td>{{ number_format($item->voltage, 2) }} V</td>
                            <td>{{ number_format($item->current, 3) }} A</td>
                            <td>{{ number_format($item->power, 2) }} W</td>
                            <td>{{ number_format($item->energy, 3) }} kWh</td>
                            <td>{{ number_format($item->frequency ?? 0, 2) }} Hz</td>
                            <td>{{ number_format($item->power_factor ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Belum ada riwayat pembacaan sensor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <p class="footer-note">
                Halaman ini auto-refresh tiap 10 detik. Untuk akses dari ESP32, gunakan IP komputer Anda, misalnya `http://192.168.59.195:8000/api/sensor-readings`.
            </p>
        </section>
    </div>
</body>
</html>
