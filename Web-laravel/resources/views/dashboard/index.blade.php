@extends('layouts.app')
@section('title', 'Dashboard - Monitoring Konsumsi Listrik')
@section('body')
@php
    $metricInfo = [
        'voltage' => [
            'label' => 'Tegangan',
            'unit' => 'V',
            'icon' => 'V',
            'value' => $latest ? number_format($latest->voltage, 2) : '--',
            'state' => 'Voltase suplai listrik. Rumah tangga umumnya berada di sekitar 220 V; turun atau naik jauh bisa mengganggu perangkat.',
            'foot' => 'Stabilitas sumber',
            'attr' => 'data-voltage-value',
        ],
        'current' => [
            'label' => 'Arus',
            'unit' => 'A',
            'icon' => 'A',
            'value' => $latest ? number_format($latest->current, 3) : '--',
            'state' => 'Besarnya aliran listrik yang sedang dipakai beban. Jika melewati batas aman, dashboard memberi warning.',
            'foot' => 'Batas aman '.$warningCurrentThreshold.' A',
            'attr' => 'data-current-value',
        ],
        'power' => [
            'label' => 'Daya',
            'unit' => 'W',
            'icon' => 'W',
            'value' => $latest ? number_format($latest->power, 2) : '--',
            'state' => 'Konsumsi listrik sesaat. Nilai ini naik saat perangkat yang menyala semakin berat.',
            'foot' => 'Beban saat ini',
            'attr' => 'data-power-value',
        ],
        'energy' => [
            'label' => 'Energi',
            'unit' => 'kWh',
            'icon' => 'E',
            'value' => $latest ? number_format($latest->energy, 3) : '--',
            'state' => 'Akumulasi pemakaian listrik. Nilai kWh ini dipakai untuk menghitung estimasi biaya.',
            'foot' => 'Akumulasi meter',
            'attr' => 'data-energy-value',
        ],
        'frequency' => [
            'label' => 'Frekuensi',
            'unit' => 'Hz',
            'icon' => 'Hz',
            'value' => $latest ? number_format($latest->frequency ?? 0, 1) : '--',
            'state' => 'Jumlah siklus listrik AC per detik. Di Indonesia nilainya idealnya mendekati 50 Hz.',
            'foot' => 'Kualitas jaringan',
            'attr' => 'data-frequency-value',
        ],
        'power_factor' => [
            'label' => 'Power Factor',
            'unit' => '',
            'icon' => 'PF',
            'value' => $latest ? number_format($latest->power_factor ?? 0, 2) : '--',
            'state' => 'Efisiensi pemakaian daya. Semakin mendekati 1, semakin baik daya listrik dimanfaatkan.',
            'foot' => 'Efisiensi beban',
            'attr' => 'data-power-factor-value',
        ],
    ];
@endphp
<button type="button" class="mobile-nav-toggle" data-mobile-nav-toggle aria-label="Toggle navigation">
    <span class="line"></span>
    <span class="line"></span>
    <span class="line"></span>
</button>
<div class="mobile-overlay" data-mobile-overlay></div>
<div class="main">
    @include('partials.sidebar')
    <main class="content" data-dashboard-live>
        <div class="topbar">
            <div>
                <h1>Dashboard <span style="color:var(--accent)">Monitoring</span></h1>
                <div class="muted" data-updated-at>
                    Update terakhir: {{ $latest?->recorded_at?->timezone(config('app.timezone'))?->format('d M Y H:i:s') ?? '--' }}
                </div>
                <div class="muted" style="margin-top:8px" data-device-id>
                    Terminal Listrik: {{ $latest?->device_id ?? 'Belum tersedia' }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <div class="pill {{ ($latest?->status ?? 'offline') === 'normal' ? 'normal' : (($latest?->status ?? 'offline') === 'warning' ? 'warning' : 'offline') }}" data-status-pill>
                    {{ $latest ? strtoupper($latest->status) : 'OFFLINE' }}
                </div>
            </div>
        </div>

        <div class="alert warning" data-current-warning @if(!($latest && (float) $latest->current > $warningCurrentThreshold)) style="display:none" @endif>
            <strong>Peringatan arus tinggi:</strong>
            Arus melewati batas {{ number_format($warningCurrentThreshold, 3) }} A. Kurangi beban atau periksa terminal listrik.
        </div>

        <section class="cards">
            @foreach($metricInfo as $metric)
                <article class="card metric-card">
                    <div>
                        <div class="metric-top">
                            <div class="muted info-label" tabindex="0">
                                {{ $metric['label'] }}
                                <span class="tooltip">{{ $metric['state'] }}</span>
                            </div>
                            <div class="metric-icon">{{ $metric['icon'] }}</div>
                        </div>
                        <div class="value">
                            <span {{ $metric['attr'] }}>{{ $metric['value'] }}</span>
                            @if($metric['unit'])
                                <small>{{ $metric['unit'] }}</small>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="metric-strip"></div>
                        <div class="metric-foot">
                            <span>{{ $metric['foot'] }}</span>
                            <span>Live</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="card" style="margin-bottom:14px">
            <div class="card-header">
                <h3>Estimasi Biaya</h3>
                <span class="label-tag">Realtime</span>
            </div>
            <div class="cost-grid">
                <div>
                    <div class="muted" style="font-size:11px;margin-bottom:6px">
                        Energi terbaru x tarif Rp {{ number_format($tariffPerKwh, 1, ',', '.') }}/kWh
                    </div>
                    <div class="cost-value" data-estimated-cost>
                        {{ $latest ? 'Rp '.number_format($estimatedCost, 0, ',', '.') : '--' }}
                    </div>
                    <div class="summary-stack">
                        <div class="summary-row">
                            <span class="label">Daya Terbaru</span>
                            <span class="number">
                                <span data-power-summary>{{ $latest ? number_format($displayedPower ?? 0, 2) : '--' }}</span>
                                <small style="font-size:12px;color:var(--muted-2)">W</small>
                            </span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Energi Terbaru</span>
                            <span class="number">
                                <span data-energy-summary>{{ $latest ? number_format($displayedEnergy ?? 0, 3) : '--' }}</span>
                                <small style="font-size:12px;color:var(--muted-2)">kWh</small>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="realtime-chart-panel">
                    <div class="chart-title">
                        <span>Chart daya realtime</span>
                        <span data-chart-latest>{{ $latest ? number_format($displayedPower ?? 0, 2).' W' : '--' }}</span>
                    </div>
                    <div data-chart-empty class="chart-empty" style="display:none">Belum ada data chart.</div>
                    <svg class="line-chart" data-power-chart viewBox="0 0 640 210" role="img" aria-label="Chart daya realtime">
                        <g data-chart-grid></g>
                        <path data-chart-area class="area-fill" d=""></path>
                        <path data-chart-line class="series-line" d=""></path>
                        <g data-chart-points></g>
                    </svg>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header" style="margin-bottom:0">
                <h3>Riwayat Terakhir</h3>
            </div>
            <div style="overflow-x:auto;margin-top:16px">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Tegangan</th>
                            <th>Arus</th>
                            <th>Daya</th>
                            <th>Energi</th>
                            <th>Frekuensi</th>
                            <th>Power Factor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody data-history-body>
                    @forelse($history as $item)
                        <tr>
                            <td>{{ $item->recorded_at?->timezone(config('app.timezone'))?->format('d-m-Y H:i:s') }}</td>
                            <td>{{ number_format($item->voltage, 2) }} V</td>
                            <td>{{ number_format($item->current, 3) }} A</td>
                            <td>{{ number_format($item->power, 2) }} W</td>
                            <td>{{ number_format($item->energy, 3) }} kWh</td>
                            <td>{{ number_format($item->frequency ?? 0, 1) }} Hz</td>
                            <td>{{ number_format($item->power_factor ?? 0, 2) }}</td>
                            <td><span class="pill {{ $item->status }}">{{ strtoupper($item->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:32px;color:var(--muted)">Belum ada data.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dashboard = document.querySelector('[data-dashboard-live]');
    if (!dashboard) {
        return;
    }

    var latestUrl = @json(route('api.sensor-readings.latest'));
    var historyUrl = @json(route('api.sensor-readings.index'));
    var refreshIntervalMs = 5000;
    var tariffPerKwh = {{ json_encode($tariffPerKwh) }};
    var warningCurrentThreshold = {{ json_encode($warningCurrentThreshold) }};

    var updatedAtEl = dashboard.querySelector('[data-updated-at]');
    var deviceIdEl = dashboard.querySelector('[data-device-id]');
    var statusPill = dashboard.querySelector('[data-status-pill]');
    var voltageValue = dashboard.querySelector('[data-voltage-value]');
    var currentValue = dashboard.querySelector('[data-current-value]');
    var powerValue = dashboard.querySelector('[data-power-value]');
    var energyValue = dashboard.querySelector('[data-energy-value]');
    var frequencyValue = dashboard.querySelector('[data-frequency-value]');
    var powerFactorValue = dashboard.querySelector('[data-power-factor-value]');
    var estimatedCost = dashboard.querySelector('[data-estimated-cost]');
    var powerSummary = dashboard.querySelector('[data-power-summary]');
    var energySummary = dashboard.querySelector('[data-energy-summary]');
    var currentWarning = dashboard.querySelector('[data-current-warning]');
    var historyBody = dashboard.querySelector('[data-history-body]');
    var chart = dashboard.querySelector('[data-power-chart]');
    var chartGrid = dashboard.querySelector('[data-chart-grid]');
    var chartArea = dashboard.querySelector('[data-chart-area]');
    var chartLine = dashboard.querySelector('[data-chart-line]');
    var chartPoints = dashboard.querySelector('[data-chart-points]');
    var chartEmpty = dashboard.querySelector('[data-chart-empty]');
    var chartLatest = dashboard.querySelector('[data-chart-latest]');

    var number1 = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
    var number2 = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    var number3 = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
    var currency = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[character];
        });
    }

    function formatDateTime(value) {
        if (!value) {
            return '--';
        }

        var date = new Date(value);
        if (isNaN(date.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }).format(date).replaceAll('/', '-');
    }

    function setStatus(status) {
        var normalized = String(status || 'offline').toLowerCase();
        if (!statusPill) {
            return;
        }

        statusPill.classList.remove('normal', 'warning', 'offline');
        statusPill.classList.add(['normal', 'warning'].includes(normalized) ? normalized : 'offline');
        statusPill.textContent = normalized.toUpperCase();
    }

    function updateCurrentWarning(current) {
        if (!currentWarning) {
            return;
        }

        currentWarning.style.display = typeof current === 'number' && current > warningCurrentThreshold
            ? 'block'
            : 'none';
    }

    function setLatest(reading) {
        if (!reading) {
            if (voltageValue) voltageValue.textContent = '--';
            if (currentValue) currentValue.textContent = '--';
            if (powerValue) powerValue.textContent = '--';
            if (energyValue) energyValue.textContent = '--';
            if (frequencyValue) frequencyValue.textContent = '--';
            if (powerFactorValue) powerFactorValue.textContent = '--';
            if (estimatedCost) estimatedCost.textContent = '--';
            if (powerSummary) powerSummary.textContent = '--';
            if (energySummary) energySummary.textContent = '--';
            if (updatedAtEl) updatedAtEl.textContent = 'Update terakhir: --';
            if (deviceIdEl) deviceIdEl.textContent = 'Terminal Listrik: Belum tersedia';
            if (chartLatest) chartLatest.textContent = '--';
            setStatus('offline');
            updateCurrentWarning(null);
            return;
        }

        var voltage = Number(reading.voltage || 0);
        var current = Number(reading.current || 0);
        var power = Number(reading.power || 0);
        var energy = Number(reading.energy || 0);
        var frequency = Number(reading.frequency || 0);
        var powerFactor = Number(reading.power_factor || 0);
        var cost = energy * tariffPerKwh;

        if (voltageValue) voltageValue.textContent = number2.format(voltage);
        if (currentValue) currentValue.textContent = number3.format(current);
        if (powerValue) powerValue.textContent = number2.format(power);
        if (energyValue) energyValue.textContent = number3.format(energy);
        if (frequencyValue) frequencyValue.textContent = number1.format(frequency);
        if (powerFactorValue) powerFactorValue.textContent = number2.format(powerFactor);
        if (estimatedCost) estimatedCost.textContent = 'Rp ' + currency.format(cost);
        if (powerSummary) powerSummary.textContent = number2.format(power);
        if (energySummary) energySummary.textContent = number3.format(energy);
        if (updatedAtEl) updatedAtEl.textContent = 'Update terakhir: ' + formatDateTime(reading.recorded_at);
        if (deviceIdEl) deviceIdEl.textContent = 'Terminal Listrik: ' + (reading.device_id || 'Belum tersedia');
        if (chartLatest) chartLatest.textContent = number2.format(power) + ' W';

        setStatus(reading.status);
        updateCurrentWarning(current);
    }

    function renderHistory(readings) {
        if (!historyBody) {
            return;
        }

        if (!readings.length) {
            historyBody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted)">Belum ada data.</td></tr>';
            renderPowerChart([]);
            return;
        }

        historyBody.innerHTML = readings.map(function (reading) {
            var statusClass = String(reading.status || 'offline').toLowerCase();

            return [
                '<tr>',
                '<td>' + escapeHtml(formatDateTime(reading.recorded_at)) + '</td>',
                '<td>' + escapeHtml(number2.format(Number(reading.voltage || 0))) + ' V</td>',
                '<td>' + escapeHtml(number3.format(Number(reading.current || 0))) + ' A</td>',
                '<td>' + escapeHtml(number2.format(Number(reading.power || 0))) + ' W</td>',
                '<td>' + escapeHtml(number3.format(Number(reading.energy || 0))) + ' kWh</td>',
                '<td>' + escapeHtml(number1.format(Number(reading.frequency || 0))) + ' Hz</td>',
                '<td>' + escapeHtml(number2.format(Number(reading.power_factor || 0))) + '</td>',
                '<td><span class="pill ' + escapeHtml(statusClass) + '">' + escapeHtml(statusClass.toUpperCase()) + '</span></td>',
                '</tr>'
            ].join('');
        }).join('');

        renderPowerChart(readings.slice().reverse());
    }

    function renderPowerChart(readings) {
        if (!chart || !chartGrid || !chartArea || !chartLine || !chartPoints || !chartEmpty) {
            return;
        }

        var values = readings.map(function (reading) {
            return {
                power: Number(reading.power || 0),
                time: formatDateTime(reading.recorded_at).slice(11, 19)
            };
        });

        if (!values.length) {
            chart.style.display = 'none';
            chartEmpty.style.display = 'grid';
            chartArea.setAttribute('d', '');
            chartLine.setAttribute('d', '');
            chartPoints.innerHTML = '';
            return;
        }

        chart.style.display = 'block';
        chartEmpty.style.display = 'none';

        var width = 640;
        var height = 210;
        var padX = 22;
        var padY = 18;
        var innerWidth = width - padX * 2;
        var innerHeight = height - padY * 2;
        var maxPower = Math.max.apply(null, values.map(function (item) { return item.power; }));
        var scaleMax = Math.max(maxPower, 1);

        chartGrid.innerHTML = [0.25, 0.5, 0.75, 1].map(function (ratio) {
            var y = padY + innerHeight * ratio;
            return '<line class="grid-line" x1="' + padX + '" y1="' + y + '" x2="' + (width - padX) + '" y2="' + y + '"></line>';
        }).join('');

        var points = values.map(function (item, index) {
            var x = values.length === 1
                ? width / 2
                : padX + (innerWidth * index / (values.length - 1));
            var y = padY + innerHeight - (item.power / scaleMax * innerHeight);
            return { x: x, y: y, power: item.power, time: item.time };
        });

        var linePath = points.map(function (point, index) {
            return (index === 0 ? 'M ' : 'L ') + point.x.toFixed(1) + ' ' + point.y.toFixed(1);
        }).join(' ');
        var areaPath = linePath + ' L ' + points[points.length - 1].x.toFixed(1) + ' ' + (height - padY) + ' L ' + points[0].x.toFixed(1) + ' ' + (height - padY) + ' Z';

        chartLine.setAttribute('d', linePath);
        chartArea.setAttribute('d', areaPath);
        chartPoints.innerHTML = points.map(function (point) {
            return '<circle class="series-point" cx="' + point.x.toFixed(1) + '" cy="' + point.y.toFixed(1) + '" r="4"><title>' + escapeHtml(point.time + ' - ' + number2.format(point.power) + ' W') + '</title></circle>';
        }).join('');
    }

    async function refreshDashboard() {
        try {
            var responses = await Promise.all([
                fetch(latestUrl, { headers: { Accept: 'application/json' } }),
                fetch(historyUrl, { headers: { Accept: 'application/json' } })
            ]);

            if (responses[0].ok) {
                var latestPayload = await responses[0].json();
                setLatest(latestPayload && latestPayload.data ? latestPayload.data : null);
            }

            if (responses[1].ok) {
                var historyPayload = await responses[1].json();
                var readings = historyPayload && historyPayload.data && Array.isArray(historyPayload.data.data)
                    ? historyPayload.data.data.slice(0, 10)
                    : [];
                renderHistory(readings);
            }
        } catch (error) {
            console.error('Gagal memuat data dashboard realtime:', error);
        }
    }

    refreshDashboard();
    window.setInterval(refreshDashboard, refreshIntervalMs);
});
</script>
@endsection
