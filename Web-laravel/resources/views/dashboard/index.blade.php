@extends('layouts.app')
@section('title', 'Dashboard - Monitoring Konsumsi Listrik')
@section('body')
@php
    $metricInfo = [
        'voltage' => [
            'label' => 'Tegangan',
            'unit' => 'V',
            'icon' => 'V',
            'value' => $latest ? number_format($latest->voltage, 0, ',', '.') : '--',
            'state' => 'Voltase suplai listrik. Rumah tangga umumnya berada di sekitar 220 V; turun atau naik jauh bisa mengganggu perangkat.',
            'foot' => 'Stabilitas sumber',
            'attr' => 'data-voltage-value',
        ],
        'current' => [
            'label' => 'Arus',
            'unit' => 'A',
            'icon' => 'A',
            'value' => $latest ? number_format($latest->current, 0, ',', '.') : '--',
            'state' => 'Besarnya aliran listrik yang sedang dipakai beban. Jika melewati batas aman, dashboard memberi warning.',
            'foot' => 'Batas aman '.$warningCurrentThreshold.' A',
            'attr' => 'data-current-value',
        ],
        'power' => [
            'label' => 'Daya',
            'unit' => 'W',
            'icon' => 'W',
            'value' => $latest ? number_format($latest->power, 0, ',', '.') : '--',
            'state' => 'Konsumsi listrik sesaat. Nilai ini naik saat perangkat yang menyala semakin berat.',
            'foot' => 'Beban saat ini',
            'attr' => 'data-power-value',
        ],
        'energy' => [
            'label' => 'Energi',
            'unit' => 'kWh',
            'icon' => 'E',
            'value' => $latest ? number_format($latest->energy, 0, ',', '.') : '--',
            'state' => 'Akumulasi pemakaian listrik. Nilai kWh ini dipakai untuk menghitung estimasi biaya.',
            'foot' => 'Akumulasi meter',
            'attr' => 'data-energy-value',
        ],
        'frequency' => [
            'label' => 'Frekuensi',
            'unit' => 'Hz',
            'icon' => 'Hz',
            'value' => $latest ? number_format($latest->frequency ?? 0, 0, ',', '.') : '--',
            'state' => 'Jumlah siklus listrik AC per detik. Di Indonesia nilainya idealnya mendekati 50 Hz.',
            'foot' => 'Kualitas jaringan',
            'attr' => 'data-frequency-value',
        ],
        'power_factor' => [
            'label' => 'Power Factor',
            'unit' => '',
            'icon' => 'PF',
            'value' => $latest ? number_format($latest->power_factor ?? 0, 0, ',', '.') : '--',
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
                        <span data-chart-title-label>Chart Daya Harian</span>
                        <span data-chart-avg-badge class="chart-avg-badge">--</span>
                    </div>

                    <!-- Filter bar -->
                    <div class="chart-filter-bar">
                        <div class="chart-filter-group" style="max-width: 200px;">
                            <label class="chart-filter-label" for="chartMetricSelect">Metrik</label>
                            <select id="chartMetricSelect" class="chart-filter-select">
                                <option value="power" selected>Daya (W)</option>
                                <option value="current">Arus (A)</option>
                                <option value="voltage">Tegangan (V)</option>
                                <option value="energy">Energi (kWh)</option>
                                <option value="frequency">Frekuensi (Hz)</option>
                                <option value="power_factor">Power Factor</option>
                            </select>
                        </div>
                    </div>

                    <div data-chart-empty class="chart-empty" style="display:none">Belum ada data untuk hari ini.</div>
                    <div data-chart-loading class="chart-loading" style="display:none">
                        <span class="chart-spinner"></span> Memuat data…
                    </div>
                    <div data-chart-wrapper>
                        <svg class="line-chart" data-power-chart viewBox="0 0 640 210" role="img" aria-label="Chart daya harian">
                            <g data-chart-grid></g>
                            <path data-chart-area class="area-fill" d=""></path>
                            <path data-chart-line class="series-line" d=""></path>
                            <g data-chart-points></g>
                        </svg>
                    </div>
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
<style>
/* ============================================
   CHART FILTER BAR
============================================ */
.chart-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 10px;
    margin-bottom: 14px;
    padding: 10px 12px;
    background: rgba(0,0,0,0.18);
    border: 1px solid var(--line);
    border-radius: 4px;
}
.chart-filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
    min-width: 130px;
}
.chart-filter-label {
    font-family: 'Space Mono', monospace;
    font-size: 9px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--muted);
}
.chart-filter-select,
.chart-filter-input {
    background: var(--bg-2);
    border: 1px solid var(--line-2);
    border-radius: 3px;
    color: var(--text);
    font-family: 'Space Mono', monospace;
    font-size: 11px;
    padding: 7px 10px;
    outline: none;
    transition: border-color 0.15s;
    width: 100%;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    min-height: 34px;
}
.chart-filter-select:focus,
.chart-filter-input:focus {
    border-color: var(--accent-2);
    box-shadow: 0 0 0 2px rgba(0,212,255,0.12);
}
.chart-filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    flex-wrap: wrap;
}
.chart-btn {
    border-radius: 3px;
    font-family: 'Space Mono', monospace;
    font-size: 9px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    min-height: 34px;
    padding: 0 12px;
    transition: all 0.15s;
    white-space: nowrap;
}
.chart-btn-outline {
    background: transparent;
    border: 1px solid var(--line-2);
    color: var(--muted-2);
}
.chart-btn-outline:hover {
    border-color: var(--accent-2);
    color: var(--accent-2);
    box-shadow: 0 0 8px rgba(0,212,255,0.15);
}
.chart-btn-primary {
    background: rgba(0,212,255,0.12);
    border: 1px solid rgba(0,212,255,0.35);
    color: var(--accent-2);
    font-weight: 700;
}
.chart-btn-primary:hover {
    background: rgba(0,212,255,0.22);
    box-shadow: 0 0 12px rgba(0,212,255,0.25);
}
.chart-avg-badge {
    font-family: 'Rajdhani', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--accent-2);
    background: rgba(0,212,255,0.08);
    border: 1px solid rgba(0,212,255,0.2);
    border-radius: 3px;
    padding: 2px 10px;
    letter-spacing: 0.04em;
}
.chart-loading {
    height: 210px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: var(--muted);
    font-family: 'Space Mono', monospace;
    font-size: 11px;
}
.chart-spinner {
    display: inline-block;
    width: 16px; height: 16px;
    border: 2px solid rgba(0,212,255,0.25);
    border-top-color: var(--accent-2);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
/* Y-axis labels */
.chart-y-label {
    font-family: 'Space Mono', monospace;
    font-size: 9px;
    fill: rgba(136,145,170,0.55);
}
/* Tooltip hover line */
.chart-hover-line {
    stroke: rgba(0,212,255,0.3);
    stroke-width: 1;
    stroke-dasharray: 3 3;
    pointer-events: none;
}
@media (max-width: 640px) {
    .chart-filter-bar { gap: 8px; padding: 8px; }
    .chart-filter-group { min-width: 100px; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dashboard = document.querySelector('[data-dashboard-live]');
    if (!dashboard) return;

    /* ── URLs ─────────────────────────────────────────── */
    var latestUrl    = @json(route('api.sensor-readings.latest'));
    var historyUrl   = @json(route('api.sensor-readings.index'));
    var dailyChartUrl = @json(route('api.sensor-readings.daily-chart'));
    var refreshIntervalMs = 5000;
    var tariffPerKwh = {{ json_encode($tariffPerKwh) }};
    var warningCurrentThreshold = {{ json_encode($warningCurrentThreshold) }};
    var appTz = 'Asia/Jakarta';

    /* ── DOM refs (realtime metrics) ─────────────────── */
    var updatedAtEl      = dashboard.querySelector('[data-updated-at]');
    var deviceIdEl       = dashboard.querySelector('[data-device-id]');
    var statusPill       = dashboard.querySelector('[data-status-pill]');
    var voltageValue     = dashboard.querySelector('[data-voltage-value]');
    var currentValue     = dashboard.querySelector('[data-current-value]');
    var powerValue       = dashboard.querySelector('[data-power-value]');
    var energyValue      = dashboard.querySelector('[data-energy-value]');
    var frequencyValue   = dashboard.querySelector('[data-frequency-value]');
    var powerFactorValue = dashboard.querySelector('[data-power-factor-value]');
    var estimatedCost    = dashboard.querySelector('[data-estimated-cost]');
    var powerSummary     = dashboard.querySelector('[data-power-summary]');
    var energySummary    = dashboard.querySelector('[data-energy-summary]');
    var currentWarning   = dashboard.querySelector('[data-current-warning]');
    var historyBody      = dashboard.querySelector('[data-history-body]');

    /* ── DOM refs (chart) ────────────────────────────── */
    var chart         = dashboard.querySelector('[data-power-chart]');
    var chartGrid     = dashboard.querySelector('[data-chart-grid]');
    var chartArea     = dashboard.querySelector('[data-chart-area]');
    var chartLine     = dashboard.querySelector('[data-chart-line]');
    var chartPoints   = dashboard.querySelector('[data-chart-points]');
    var chartEmpty    = dashboard.querySelector('[data-chart-empty]');
    var chartLoading  = dashboard.querySelector('[data-chart-loading]');
    var chartWrapper  = dashboard.querySelector('[data-chart-wrapper]');
    var chartTitleLabel = dashboard.querySelector('[data-chart-title-label]');
    var chartAvgBadge   = dashboard.querySelector('[data-chart-avg-badge]');

    /* ── Filter controls ─────────────────────────────── */
    var metricSelect  = document.getElementById('chartMetricSelect');

    /* ── Formatters ──────────────────────────────────── */
    var number0  = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    var currency = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

    var metricMeta = {
        power:        { label: 'Daya',         unit: 'W',   title: 'Chart Daya Realtime',       fmt: number0,  color: 'rgba(0,212,255,1)', glow: 'rgba(0,212,255,0.35)', area: 'rgba(0,212,255,0.10)' },
        current:      { label: 'Arus',         unit: 'A',   title: 'Chart Arus Realtime',       fmt: number0,  color: 'rgba(240,192,0,1)', glow: 'rgba(240,192,0,0.35)', area: 'rgba(240,192,0,0.10)' },
        voltage:      { label: 'Tegangan',     unit: 'V',   title: 'Chart Tegangan Realtime',   fmt: number0,  color: 'rgba(0,230,118,1)', glow: 'rgba(0,230,118,0.35)', area: 'rgba(0,230,118,0.10)' },
        energy:       { label: 'Energi',       unit: 'kWh', title: 'Chart Energi Realtime',     fmt: number0,  color: 'rgba(255,112,0,1)', glow: 'rgba(255,112,0,0.30)', area: 'rgba(255,112,0,0.08)' },
        frequency:    { label: 'Frekuensi',    unit: 'Hz',  title: 'Chart Frekuensi Realtime',  fmt: number0,  color: 'rgba(179,136,255,1)', glow: 'rgba(179,136,255,0.35)', area: 'rgba(179,136,255,0.10)' },
        power_factor: { label: 'Power Factor', unit: '',    title: 'Chart Power Factor Realtime', fmt: number0,  color: 'rgba(255,77,77,1)',  glow: 'rgba(255,77,77,0.30)', area: 'rgba(255,77,77,0.08)' },
    };

    /* ── Helper ──────────────────────────────────────── */
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function formatDateTime(value) {
        if (!value) return '--';
        var date = new Date(value);
        if (isNaN(date.getTime())) return value;
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        }).format(date).replaceAll('/', '-');
    }

    function todayLocalDate() {
        var now = new Date();
        var utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        // appTz is Asia/Jakarta which is UTC+7
        var jakartaTime = new Date(utc + (3600000 * 7));
        var yyyy = jakartaTime.getFullYear();
        var mm = String(jakartaTime.getMonth() + 1).padStart(2, '0');
        var dd = String(jakartaTime.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }
    function yesterdayLocalDate() {
        var now = new Date();
        var utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        // appTz is Asia/Jakarta which is UTC+7
        var jakartaTime = new Date(utc + (3600000 * 7));
        jakartaTime.setDate(jakartaTime.getDate() - 1);
        var yyyy = jakartaTime.getFullYear();
        var mm = String(jakartaTime.getMonth() + 1).padStart(2, '0');
        var dd = String(jakartaTime.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }

    /* ── Status helpers ──────────────────────────────── */
    function setStatus(status) {
        var normalized = String(status || 'offline').toLowerCase();
        if (!statusPill) return;
        statusPill.classList.remove('normal', 'warning', 'offline');
        statusPill.classList.add(['normal', 'warning'].includes(normalized) ? normalized : 'offline');
        statusPill.textContent = normalized.toUpperCase();
    }

    function updateCurrentWarning(current) {
        if (!currentWarning) return;
        currentWarning.style.display = typeof current === 'number' && current > warningCurrentThreshold ? 'block' : 'none';
    }

    /* ── Realtime: setLatest ─────────────────────────── */
    function setLatest(reading) {
        if (!reading) {
            if (voltageValue)     voltageValue.textContent     = '--';
            if (currentValue)     currentValue.textContent     = '--';
            if (powerValue)       powerValue.textContent       = '--';
            if (energyValue)      energyValue.textContent      = '--';
            if (frequencyValue)   frequencyValue.textContent   = '--';
            if (powerFactorValue) powerFactorValue.textContent = '--';
            if (estimatedCost)    estimatedCost.textContent    = '--';
            if (powerSummary)     powerSummary.textContent     = '--';
            if (energySummary)    energySummary.textContent    = '--';
            if (updatedAtEl)      updatedAtEl.textContent      = 'Update terakhir: --';
            if (deviceIdEl)       deviceIdEl.textContent       = 'Terminal Listrik: Belum tersedia';
            setStatus('offline');
            updateCurrentWarning(null);
            return;
        }
        var voltage     = Number(reading.voltage      || 0);
        var current     = Number(reading.current      || 0);
        var power       = Number(reading.power        || 0);
        var energy      = Number(reading.energy       || 0);
        var frequency   = Number(reading.frequency    || 0);
        var powerFactor = Number(reading.power_factor || 0);
        var cost        = energy * tariffPerKwh;

        if (voltageValue)     voltageValue.textContent     = number0.format(voltage);
        if (currentValue)     currentValue.textContent     = number0.format(current);
        if (powerValue)       powerValue.textContent       = number0.format(power);
        if (energyValue)      energyValue.textContent      = number0.format(energy);
        if (frequencyValue)   frequencyValue.textContent   = number0.format(frequency);
        if (powerFactorValue) powerFactorValue.textContent = number0.format(powerFactor);
        if (estimatedCost)    estimatedCost.textContent    = 'Rp ' + currency.format(cost);
        if (powerSummary)     powerSummary.textContent     = number0.format(power);
        if (energySummary)    energySummary.textContent    = number0.format(energy);
        if (updatedAtEl)      updatedAtEl.textContent      = 'Update terakhir: ' + formatDateTime(reading.recorded_at);
        if (deviceIdEl)       deviceIdEl.textContent       = 'Terminal Listrik: ' + (reading.device_id || 'Belum tersedia');
        setStatus(reading.status);
        updateCurrentWarning(current);
    }

    /* ── Realtime: renderHistory ─────────────────────── */
    function renderHistory(readings) {
        if (!historyBody) return;
        if (!readings.length) {
            historyBody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted)">Belum ada data.</td></tr>';
            return;
        }
        historyBody.innerHTML = readings.map(function (r) {
            var sc = String(r.status || 'offline').toLowerCase();
            return [
                '<tr>',
                '<td>' + escapeHtml(formatDateTime(r.recorded_at)) + '</td>',
                '<td>' + escapeHtml(number0.format(Number(r.voltage      || 0))) + ' V</td>',
                '<td>' + escapeHtml(number0.format(Number(r.current      || 0))) + ' A</td>',
                '<td>' + escapeHtml(number0.format(Number(r.power        || 0))) + ' W</td>',
                '<td>' + escapeHtml(number0.format(Number(r.energy       || 0))) + ' kWh</td>',
                '<td>' + escapeHtml(number0.format(Number(r.frequency    || 0))) + ' Hz</td>',
                '<td>' + escapeHtml(number0.format(Number(r.power_factor || 0))) + '</td>',
                '<td><span class="pill ' + escapeHtml(sc) + '">' + escapeHtml(sc.toUpperCase()) + '</span></td>',
                '</tr>'
            ].join('');
        }).join('');
    }

    /* ── Chart: render ───────────────────────────────── */
    function renderChart(points, metric, isRealtime) {
        if (!chart || !chartGrid || !chartArea || !chartLine || !chartPoints || !chartEmpty || !chartWrapper) return;

        var meta = metricMeta[metric] || metricMeta.power;

        // Filter only non-null values to check for data
        var values = points.filter(function (p) { return p.value !== null; });

        if (!values.length) {
            chartWrapper.style.display = 'none';
            chartEmpty.style.display = 'grid';
            chartArea.setAttribute('d', '');
            chartLine.setAttribute('d', '');
            chartPoints.innerHTML = '';
            chartGrid.innerHTML = '';
            if (chartAvgBadge) chartAvgBadge.textContent = '--';
            return;
        }

        chartWrapper.style.display = 'block';
        chartEmpty.style.display = 'none';

        // Update line/area colors dynamically
        chartLine.style.stroke  = meta.color;
        chartLine.style.filter  = 'drop-shadow(0 0 7px ' + meta.glow + ')';
        chartArea.style.fill    = meta.area;

        // Average or Current value badge
        var unit = meta.unit ? ' ' + meta.unit : '';
        if (isRealtime) {
            var latestVal = values[values.length - 1].value;
            if (chartAvgBadge) chartAvgBadge.textContent = 'Saat Ini: ' + meta.fmt.format(latestVal) + unit;
        } else {
            var sum = values.reduce(function (acc, p) { return acc + p.value; }, 0);
            var avg = sum / values.length;
            if (chartAvgBadge) chartAvgBadge.textContent = 'Rata-rata: ' + meta.fmt.format(avg) + unit;
        }

        var width     = 640;
        var height    = 210;
        var padX      = 46;  // wider left pad for y-axis labels
        var padY      = 18;
        var padRight  = 10;
        var innerW    = width - padX - padRight;
        var innerH    = height - padY * 2;

        var allValues = points.map(function (p) { return p.value !== null ? p.value : 0; });
        var maxVal    = Math.max.apply(null, allValues);
        var minVal    = Math.min.apply(null, values.map(function (p) { return p.value; }));
        var scaleMax  = Math.max(maxVal, 1);
        var scaleMin  = Math.max(minVal * 0.9, 0);
        var scaleRange = scaleMax - scaleMin || 1;

        // Grid lines + Y-axis labels
        chartGrid.innerHTML = [0.25, 0.5, 0.75, 1].map(function (ratio) {
            var y      = padY + innerH * ratio;
            var yVal   = scaleMax - (ratio * scaleRange);
            var yLabel = meta.fmt.format(yVal);
            return '<line class="grid-line" x1="' + padX + '" y1="' + y + '" x2="' + (width - padRight) + '" y2="' + y + '"></line>'
                 + '<text class="chart-y-label" x="' + (padX - 4) + '" y="' + (y + 3) + '" text-anchor="end">' + escapeHtml(yLabel) + '</text>';
        }).join('');

        // Map all hours/points to SVG points
        var svgPoints = points.map(function (p, i) {
            var x = points.length === 1 ? padX + innerW / 2 : padX + (innerW * i / (points.length - 1));
            var v = p.value !== null ? p.value : null;
            var y = v !== null ? padY + innerH - ((v - scaleMin) / scaleRange * innerH) : null;
            return { x: x, y: y, value: v, label: p.label };
        });

        // Build path skipping null segments
        var lineParts = [];
        var areaParts = [];
        var segment   = [];

        function pushSegment(seg) {
            if (seg.length < 1) return;
            var lp = seg.map(function (pt, idx) {
                return (idx === 0 ? 'M ' : 'L ') + pt.x.toFixed(1) + ' ' + pt.y.toFixed(1);
            }).join(' ');
            lineParts.push(lp);
            var ap = lp
                + ' L ' + seg[seg.length-1].x.toFixed(1) + ' ' + (height - padY)
                + ' L ' + seg[0].x.toFixed(1) + ' ' + (height - padY) + ' Z';
            areaParts.push(ap);
        }

        svgPoints.forEach(function (pt) {
            if (pt.y !== null) {
                segment.push(pt);
            } else {
                pushSegment(segment);
                segment = [];
            }
        });
        pushSegment(segment);

        chartLine.setAttribute('d', lineParts.join(' '));
        chartArea.setAttribute('d', areaParts.join(' '));

        // Dots only for non-null
        chartPoints.innerHTML = svgPoints.filter(function (pt) { return pt.y !== null; }).map(function (pt) {
            var tip = escapeHtml(pt.label + ' — ' + meta.fmt.format(pt.value) + unit);
            return '<circle class="series-point" cx="' + pt.x.toFixed(1) + '" cy="' + pt.y.toFixed(1) + '" r="3.5">'
                 + '<title>' + tip + '</title></circle>';
        }).join('');
    }

    /* ── Chart: fetch from API ───────────────────────── */
    /* ── Realtime: dashboard refresh (with realtime chart today) ──────── */
    async function refreshDashboard() {
        try {
            var responses = await Promise.all([
                fetch(latestUrl,  { headers: { Accept: 'application/json' } }),
                fetch(historyUrl, { headers: { Accept: 'application/json' } })
            ]);
            if (responses[0].ok) {
                var latestPayload = await responses[0].json();
                setLatest(latestPayload && latestPayload.data ? latestPayload.data : null);
            }
            if (responses[1].ok) {
                var historyPayload = await responses[1].json();
                var allReadings = historyPayload && historyPayload.data && Array.isArray(historyPayload.data.data)
                    ? historyPayload.data.data
                    : [];
                
                // History table shows latest 10
                renderHistory(allReadings.slice(0, 10));

                var metric = metricSelect ? metricSelect.value : 'power';
                var meta = metricMeta[metric] || metricMeta.power;
                if (chartTitleLabel) chartTitleLabel.textContent = meta.title.replace('Harian', 'Realtime');

                var chartPointsArray = allReadings.slice(0, 50).reverse().map(function (r) {
                    return {
                        label: formatDateTime(r.recorded_at).slice(11, 19),
                        value: r[metric] !== undefined && r[metric] !== null ? Number(r[metric]) : null
                    };
                });
                renderChart(chartPointsArray, metric, true);
            }
        } catch (error) {
            console.error('Gagal memuat data dashboard realtime:', error);
        }
    }

    if (metricSelect) {
        metricSelect.addEventListener('change', refreshDashboard);
    }

    /* ── Init ────────────────────────────────────────── */
    refreshDashboard();
    window.setInterval(refreshDashboard, refreshIntervalMs);
});
</script>
@endsection
