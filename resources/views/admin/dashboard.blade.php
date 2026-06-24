@extends('layouts.admin')

@section('title', 'Dashboard — BHUMI Admin')

@push('styles')
<style>
    /* Custom Bootstrap-style Pagination */
    .pagination { display: flex; padding-left: 0; list-style: none; border-radius: 0.25rem; margin: 0; }
    .page-item.disabled .page-link { color: #6c757d; pointer-events: none; background-color: #fff; border-color: #dee2e6; }
    .page-item.active .page-link { z-index: 3; color: #fff; background-color: #1f5c47; border-color: #1f5c47; }
    .page-link { position: relative; display: block; padding: 0.5rem 0.75rem; margin-left: -1px; line-height: 1.25; color: #1f5c47; background-color: #fff; border: 1px solid #dee2e6; text-decoration: none; }
    .page-link:hover { z-index: 2; color: #173f31; text-decoration: none; background-color: #e9ecef; border-color: #dee2e6; }
    .page-item:first-child .page-link { border-top-left-radius: 0.25rem; border-bottom-left-radius: 0.25rem; }
    .page-item:last-child .page-link { border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem; }
</style>
@endpush

@section('content')
    <div class="admin-page-header">
        <h1>Dashboard</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
        <div class="stat-card cercospora">
            <div class="stat-card-title">Terdeteksi</div>
            <div class="stat-card-label">Bercak Abu</div>
            <div class="stat-card-value">{{ number_format($cercosporaCount) }}</div>
        </div>
        <div class="stat-card common-rust">
            <div class="stat-card-title">Terdeteksi</div>
            <div class="stat-card-label">Karat Daun</div>
            <div class="stat-card-value">{{ number_format($commonRustCount) }}</div>
        </div>
        <div class="stat-card northern-blight">
            <div class="stat-card-title">Terdeteksi</div>
            <div class="stat-card-label">Hawar Daun</div>
            <div class="stat-card-value">{{ number_format($northernLeafBlightCount) }}</div>
        </div>
        <div class="stat-card healthy">
            <div class="stat-card-title">Terdeteksi</div>
            <div class="stat-card-label">Sehat</div>
            <div class="stat-card-value">{{ number_format($healthyCount) }}</div>
        </div>
    </div>

    <div class="dashboard-grid-main" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 28px;">
        {{-- Chart --}}
        <div class="chart-container" style="margin-bottom: 0;">
            <div class="dashboard-filter-wrap" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0;">Tren Hasil Deteksi
                    {{ $filter == 'weekly' ? '(4 Minggu Terakhir)' : ($filter == 'monthly' ? '(12 Bulan Terakhir)' : ($filter == 'yearly' ? '(Semua Data)' : '(7 Hari Terakhir)')) }}
                </h3>
                <select
                    style="padding: 6px 12px; border-radius: var(--admin-radius-sm); border: 1.5px solid #e5e7eb; font-size: 0.85rem; font-family: inherit; color: var(--admin-text); background: var(--admin-white); cursor: pointer;"
                    onchange="window.location.href='?filter='+this.value">
                    <option value="daily" {{ $filter == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $filter == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div id="detectionChart"></div>
        </div>

        {{-- Speedometer / Gauge --}}
        <div class="chart-container gauge-card" id="monitoring"
            style="margin-bottom: 0; padding: 0; display: flex; flex-direction: column;">
            <div style="padding: 24px; flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h3 style="margin-bottom: 4px; font-size: 1.2rem;">Rata-Rata Performa</h3>
                        <p style="font-size: 0.85rem; color: #6b7280; margin: 0;">Akurasi model berdasarkan hasil deteksi
                        </p>
                    </div>
                </div>

                <div id="gaugeChart" style="width: 100%; height: 240px; margin-top: 10px;"></div>

                <div
                    style="text-align: center; margin-top: -20px; font-size: 0.9rem; color: #6b7280; line-height: 1.5; padding: 0 20px;">
                    Rata-rata akurasi model adalah <span
                        style="font-weight: 600; color: var(--admin-text);">{{ number_format($averageConfidence, 2) }}%</span>
                    dari total deteksi.
                </div>
            </div>

            <div
                style="background: #f8fafc; border-top: 1px solid #f1f5f9; border-bottom-left-radius: var(--admin-radius); border-bottom-right-radius: var(--admin-radius); display: flex; padding: 20px 0;">
                <div style="flex: 1; text-align: center; border-right: 1px solid #e2e8f0;">
                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Penyakit</div>
                    <div
                        style="font-size: 1.1rem; font-weight: 700; color: var(--admin-text); display: flex; align-items: center; justify-content: center; gap: 4px;">
                        {{ number_format($totalDetections - $healthyCount) }} <span
                            style="color: #ef4444; font-size: 1rem;">↓</span>
                    </div>
                </div>
                <div style="flex: 1; text-align: center; border-right: 1px solid #e2e8f0;">
                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Sehat</div>
                    <div
                        style="font-size: 1.1rem; font-weight: 700; color: var(--admin-text); display: flex; align-items: center; justify-content: center; gap: 4px;">
                        {{ number_format($healthyCount) }} <span style="color: #2FA084; font-size: 1rem;">↑</span>
                    </div>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Total</div>
                    <div
                        style="font-size: 1.1rem; font-weight: 700; color: var(--admin-text); display: flex; align-items: center; justify-content: center; gap: 4px;">
                        {{ number_format($totalDetections) }} <span style="color: #2FA084; font-size: 1rem;">↑</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid-tables" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        {{-- Recent Detections --}}
        <div class="table-container" style="margin-bottom: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h3 style="margin: 0;">Riwayat Deteksi</h3>
                <form action="{{ route('admin.dashboard') }}" method="GET"
                    class="riwayat-filter-form" style="display: flex; gap: 8px; align-items: center;">
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <input type="month" name="month_filter" value="{{ request('month_filter') }}" class="form-control"
                        style="padding: 6px 12px; height: auto; min-width: 140px; cursor: pointer;" required>
                    <button type="submit" class="btn-primary filter-riwayat"
                        style="padding: 6px 16px; font-size: 0.9rem;">Filter</button>
                    @if (request('month_filter'))
                        <a href="{{ route('admin.dashboard') }}{{ request('filter') ? '?filter=' . request('filter') : '' }}"
                            class="btn-cancel"
                            style="padding: 6px 12px; font-size: 0.9rem; text-decoration: none;">Reset</a>
                    @endif
                </form>
            </div>

            @if ($recentDetections->count() > 0)
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Hasil Deteksi</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentDetections as $detection)
                            <tr>
                                <td>{{ $detection->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match ($detection->predicted_class) {
                                            'Cercospora' => 'badge-cercospora',
                                            'Common_Rust' => 'badge-common-rust',
                                            'Northern_Leaf_Blight' => 'badge-northern-blight',
                                            'Healthy' => 'badge-healthy',
                                            default => '',
                                        };
                                        $optMap = [
                                            'Cercospora' => 'Bercak Abu',
                                            'Common_Rust' => 'Karat Daun',
                                            'Northern_Leaf_Blight' => 'Hawar Daun',
                                            'Healthy' => 'Sehat'
                                        ];
                                        $displayName = $optMap[$detection->predicted_class] ?? str_replace('_', ' ', $detection->predicted_class);
                                    @endphp
                                    <span
                                        class="badge {{ $badgeClass }}">{{ $displayName }}</span>
                                </td>
                                <td>{{ number_format($detection->confidence, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 20px; display: flex; justify-content: center; width: 100%;">
                    {{ $recentDetections->links('pagination::bootstrap-4') }}
                </div>
            @else
                <p style="text-align: center; padding: 40px; color: #9ca3af;">Belum ada data deteksi.</p>
            @endif
        </div>

        {{-- Recent Products --}}
        <div class="table-container" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0;">Daftar Pestisida</h3>
                <a href="{{ route('admin.produk.index') }}"
                    style="font-size: 0.85rem; color: var(--admin-primary); font-weight: 600;">Selengkapnya</a>
            </div>
            @if ($recentProducts->count() > 0)
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Jenis</th>
                            <th>Dosis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentProducts as $produk)
                            <tr>
                                <td>{{ Str::words($produk->nama_produk, 3, '...') }}</td>
                                <td><span class="badge"
                                        style="background: rgba(47, 160, 132, 0.1); color: var(--admin-primary);">{{ $produk->jenisProduk->jenis_produk ?? 'Umum' }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #1f5c47;">
                                        {{ $produk->dosis_penggunaan }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 20px; display: flex; justify-content: center; width: 100%;">
                    {{ $recentProducts->links('pagination::bootstrap-4') }}
                </div>
            @else
                <p style="text-align: center; padding: 40px; color: #9ca3af;">Belum ada data produk.</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartDom = document.getElementById('detectionChart');
            if (!chartDom) return;

            const chart = echarts.init(chartDom);

            const option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'line'
                    }
                },
                legend: {
                    data: ['Bercak Abu', 'Karat Daun', 'Hawar Daun', 'Sehat'],
                    bottom: 0,
                    textStyle: {
                        fontSize: 12
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: @json($dates),
                    axisLabel: {
                        rotate: 45,
                        fontSize: 11
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Jumlah',
                    minInterval: 1,
                    nameTextStyle: {
                        padding: [0, 0, 0, 25]
                    }
                },
                series: @json($series)
            };

            chart.setOption(option);

            window.addEventListener('resize', function() {
                chart.resize();
            });

            // Gauge Chart Logic
            const gaugeDom = document.getElementById('gaugeChart');
            if (gaugeDom) {
                const gaugeChart = echarts.init(gaugeDom);
                const avgConf = {{ round($averageConfidence, 2) }};
                const gaugeOption = {
                    series: [{
                        type: 'gauge',
                        startAngle: 180,
                        endAngle: 0,
                        center: ['50%', '75%'],
                        radius: '90%',
                        min: 0,
                        max: 100,
                        splitNumber: 10,
                        axisLine: {
                            lineStyle: {
                                width: 16,
                                color: [
                                    [{{ min(1, max(0, $averageConfidence / 100)) }}, '#1a4731'],
                                    [1, '#E2E8F0']
                                ]
                            }
                        },
                        pointer: {
                            show: false
                        },
                        axisTick: {
                            show: false
                        },
                        splitLine: {
                            show: false
                        },
                        axisLabel: {
                            show: false
                        },
                        title: {
                            show: false
                        },
                        detail: {
                            fontSize: 36,
                            fontWeight: '800',
                            offsetCenter: [0, '-10%'],
                            valueAnimation: true,
                            formatter: function(value) {
                                return value + '%';
                            },
                            color: '#1f2937'
                        },
                        data: [{
                            value: avgConf
                        }]
                    }]
                };

                gaugeChart.setOption(gaugeOption);
                window.addEventListener('resize', function() {
                    gaugeChart.resize();
                });
            }
        });
    </script>
@endpush
