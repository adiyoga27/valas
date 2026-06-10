@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan transaksi hari ini')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md transition-shadow">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-amber-50 rounded-full"></div>
            <p class="relative text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Uang Aktif</p>
            <p class="relative mt-3 text-3xl font-bold text-gray-900">{{ $activeCurrencies }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md transition-shadow">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-emerald-50 rounded-full"></div>
            <p class="relative text-xs font-medium text-gray-500 uppercase tracking-wider">Pembelian Hari Ini</p>
            <p class="relative mt-3 text-lg font-bold text-emerald-600">Rp {{ number_format($todayBuyTotal, 0, ',', '.') }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md transition-shadow">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-red-50 rounded-full"></div>
            <p class="relative text-xs font-medium text-gray-500 uppercase tracking-wider">Penjualan Hari Ini</p>
            <p class="relative mt-3 text-lg font-bold text-red-600">Rp {{ number_format($todaySellTotal, 0, ',', '.') }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md transition-shadow">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-emerald-50 rounded-full"></div>
            <p class="relative text-xs font-medium text-gray-500 uppercase tracking-wider">Pembelian Bulan Ini</p>
            <p class="relative mt-3 text-lg font-bold text-emerald-600">Rp {{ number_format($monthBuyTotal, 0, ',', '.') }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md transition-shadow">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-red-50 rounded-full"></div>
            <p class="relative text-xs font-medium text-gray-500 uppercase tracking-wider">Penjualan Bulan Ini</p>
            <p class="relative mt-3 text-lg font-bold text-red-600">Rp {{ number_format($monthSellTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Grafik Pembelian & Penjualan</h3>
                <p class="text-sm text-gray-500 mt-0.5">Total per bulan dalam Rupiah</p>
            </div>
            <select id="yearFilter" class="w-full sm:w-auto rounded-xl border border-gray-200 px-3.5 py-2 text-sm font-medium text-gray-700 bg-white shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" onchange="window.location.href='?year='+this.value">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="relative h-80">
            <canvas id="transactionChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('transactionChart');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pembelian',
                data: @json($chartBuy),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981'
            }, {
                label: 'Penjualan',
                data: @json($chartSell),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 24, font: { size: 13 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID'); }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return 'Rp ' + (v / 1000000).toFixed(0) + 'M'; },
                        font: { size: 11 }
                    },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
