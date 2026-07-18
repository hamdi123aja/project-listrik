@extends('layouts.app')
@section('title', 'History - Monitoring Konsumsi Listrik')
@section('body')
<button type="button" class="mobile-nav-toggle" data-mobile-nav-toggle aria-label="Toggle navigation">
    <span class="line"></span>
    <span class="line"></span>
    <span class="line"></span>
</button>
<div class="mobile-overlay" data-mobile-overlay></div>
<div class="main">
    @include('partials.sidebar')
    <main class="content history-page">
        <div class="topbar history-topbar">
            <div>
                <h1 style="margin:0">History Data</h1>
                <div class="muted">Riwayat pembacaan sensor listrik</div>
            </div>
            <a href="{{ route('history.export.csv') }}" class="btn history-export-btn">Export CSV</a>
        </div>

        <section class="cards history-stats">
            <article class="card"><div class="muted">Peak Power</div><div class="value">{{ number_format($peakPower, 1) }} <small>W</small></div></article>
            <article class="card"><div class="muted">Average Voltage</div><div class="value">{{ number_format($avgVoltage, 1) }} <small>V</small></div></article>
            <article class="card"><div class="muted">Total Energy</div><div class="value">{{ number_format($totalEnergy, 2) }} <small>kWh</small></div></article>
        </section>

        <section class="card report-summary-card">
            <div class="card-header">
                <h3>Status Alat & Batas Operasi</h3>
            </div>
            <div class="report-table-wrap">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Batas Normal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Terminal Listrik</td>
                            <td>{{ $readings->count() ? ($readings->first()->device_id ?: 'Tidak diketahui') : 'Tidak tersedia' }}</td>
                            <td>-</td>
                            <td>Pengidentifikasi terminal sensor.</td>
                        </tr>
                        <tr>
                            <td>Arus</td>
                            <td>{{ $readings->count() ? number_format($currentWarningThreshold, 3).' A' : '--' }}</td>
                            <td><span class="pill normal">≤ {{ number_format($currentWarningThreshold, 3) }} A</span></td>
                            <td>Nilai arus yang dianggap aman. Melebihi nilai ini akan men-trigger peringatan.</td>
                        </tr>
                        <tr>
                            <td>Power Factor</td>
                            <td>{{ $readings->count() ? 'Normal jika ≥ 0.80' : '--' }}</td>
                            <td>≥ 0.80</td>
                            <td>Menunjukkan efisiensi penggunaan daya.</td>
                        </tr>
                        <tr>
                            <td>Daya</td>
                            <td>{{ $readings->count() ? 'Normal jika ≤ 2000 W' : '--' }}</td>
                            <td>≤ 2000 W</td>
                            <td>Estimasi beban maksimum normal.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <div class="history-table-wrap">
                <table class="table history-table">
                    <thead>
                    <tr>
                        <th>Waktu</th><th>Tegangan</th><th>Arus</th><th>Daya</th><th>Energi</th><th>Frekuensi</th><th>PF</th><th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($readings as $row)
                        <tr class="history-row" data-row-detail="{{ htmlspecialchars(json_encode([
                            'waktu' => $row->recorded_at?->timezone(config('app.timezone'))?->format('d-m-Y H:i:s'),
                            'tegangan' => number_format($row->voltage, 2).' V',
                            'arus' => number_format($row->current, 3).' A',
                            'daya' => number_format($row->power, 2).' W',
                            'energi' => number_format($row->energy, 3).' kWh',
                            'frekuensi' => number_format($row->frequency ?? 0, 2).' Hz',
                            'pf' => number_format($row->power_factor ?? 0, 2),
                            'status' => ucfirst($row->status),
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}">
                            <td>{{ $row->recorded_at?->timezone(config('app.timezone'))?->format('d-m-Y H:i:s') }}</td>
                            <td>{{ number_format($row->voltage, 2) }}</td>
                            <td>{{ number_format($row->current, 3) }}</td>
                            <td>{{ number_format($row->power, 2) }}</td>
                            <td>{{ number_format($row->energy, 3) }}</td>
                            <td>{{ number_format($row->frequency ?? 0, 2) }}</td>
                            <td>{{ number_format($row->power_factor ?? 0, 2) }}</td>
                            <td><span class="pill {{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="8">Data belum tersedia.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="history-pagination">
                <div class="history-pagination-summary">
                    Menampilkan {{ $readings->firstItem() ?? 0 }} sampai {{ $readings->lastItem() ?? 0 }} dari {{ $readings->total() }} data
                </div>
                <div class="history-pagination-actions">
                    @if ($readings->onFirstPage())
                        <span class="history-page-btn is-disabled">Previous</span>
                    @else
                        <a class="history-page-btn" href="{{ $readings->previousPageUrl() }}">Previous</a>
                    @endif

                    <span class="history-page-info">Page {{ $readings->currentPage() }} of {{ $readings->lastPage() }}</span>

                    @if ($readings->hasMorePages())
                        <a class="history-page-btn" href="{{ $readings->nextPageUrl() }}">Next</a>
                    @else
                        <span class="history-page-btn is-disabled">Next</span>
                    @endif
                </div>
            </div>
        </section>

        <div class="history-detail-popup" id="historyDetailPopup" aria-hidden="true">
            <div class="history-detail-card">
                <header>
                    <div>
                        <h3>Detail Pembacaan</h3>
                        <p class="muted">Klik di luar atau tombol Tutup untuk kembali</p>
                    </div>
                    <button type="button" id="historyDetailClose">Tutup</button>
                </header>
                <div class="detail-grid" id="historyDetailGrid"></div>
            </div>
        </div>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('historyDetailPopup');
        const popupClose = document.getElementById('historyDetailClose');
        const popupGrid = document.getElementById('historyDetailGrid');

        function closePopup() {
            popup.classList.remove('open');
            popup.setAttribute('aria-hidden', 'true');
            popupGrid.innerHTML = '';
        }

        function openPopup(detail) {
            popupGrid.innerHTML = Object.entries(detail).map(([label, value]) => {
                return `<div class="detail-row"><strong>${label}</strong><span>${value}</span></div>`;
            }).join('');
            popup.classList.add('open');
            popup.setAttribute('aria-hidden', 'false');
        }

        document.querySelectorAll('.history-row').forEach(function (row) {
            row.addEventListener('click', function () {
                const detailJson = row.getAttribute('data-row-detail');
                if (!detailJson) { return; }
                try {
                    const detail = JSON.parse(detailJson);
                    openPopup(detail);
                } catch (error) {
                    console.error('Failed to parse history detail JSON:', error);
                }
            });
        });

        popupClose.addEventListener('click', closePopup);
        popup.addEventListener('click', function (event) {
            if (event.target === popup) {
                closePopup();
            }
        });
    });
</script>
<style>
.history-page .topbar h1 { font-size: 46px; line-height: 1.05; }
.history-stats .value { font-size:38px; }
.history-export-btn { white-space:nowrap; min-height:44px; display:inline-flex; align-items:center; }
.history-table-wrap { overflow:auto; }
.history-table { min-width: 900px; }
.history-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--line);
}
.history-pagination-summary {
    font-family: 'Space Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
}
.history-pagination-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.history-page-btn,
.history-page-info {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    border-radius: 2px;
    border: 1px solid var(--line-2);
    background: var(--bg-2);
    font-family: 'Space Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text);
}
.history-page-btn {
    transition: all 0.15s ease;
}
.history-page-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
}
.history-page-btn.is-disabled {
    opacity: 0.45;
    pointer-events: none;
}
.history-page-info {
    color: var(--muted-2);
    padding-inline: 12px;
}

.report-summary-card .report-table th,
.report-summary-card .report-table td {
    padding: 12px 14px;
    text-align: left;
}

.report-summary-card .report-table {
    min-width: 100%;
    border-collapse: collapse;
}

.history-row { cursor: pointer; }
.history-row:hover { background: rgba(255,255,255,0.06); }

.history-detail-popup {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(20, 22, 30, 0.84);
    z-index: 1000;
}

.history-detail-popup.open {
    display: flex;
}

.history-detail-card {
    width: min(560px, 100%);
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.25);
}

.history-detail-card header {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    align-items: center;
    margin-bottom: 18px;
}

.history-detail-card header h3 {
    margin: 0;
    font-size: 1.15rem;
}

.history-detail-card .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.history-detail-card .detail-row {
    background: var(--bg-2);
    padding: 12px 14px;
    border-radius: 4px;
}

.history-detail-card .detail-row strong {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    color: var(--muted);
}

.history-detail-card button {
    margin-top: 20px;
    width: 100%;
    padding: 12px 14px;
    border: none;
    border-radius: 4px;
    background: var(--accent);
    color: #08090f;
    font-weight: 700;
    cursor: pointer;
}

@media (max-width: 900px){
    .history-page .topbar h1 { font-size: 36px; }
    .history-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .history-table { min-width: 100%; }
    .history-table th,
    .history-table td { font-size: 13px; padding: 10px 8px; }
    .history-table th:nth-child(6),
    .history-table td:nth-child(6),
    .history-table th:nth-child(7),
    .history-table td:nth-child(7) { display:none; }
}

@media (max-width: 640px) {
    .history-page .topbar h1 { font-size: 32px; }
    .history-topbar { gap: 12px; }
    .history-topbar .muted { font-size: 14px; }
    .history-export-btn { font-size: 13px; padding: 10px 12px; }
    .history-stats { grid-template-columns: 1fr; }
    .history-stats .value { font-size: 34px; }
    .history-table th:nth-child(3),
    .history-table td:nth-child(3),
    .history-table th:nth-child(5),
    .history-table td:nth-child(5) { display:none; }
    .history-pagination { align-items: stretch; }
    .history-pagination-actions { width: 100%; justify-content: space-between; }
}
</style>
@endsection
