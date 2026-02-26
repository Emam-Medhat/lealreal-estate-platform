@extends('layouts.app')

@section('title', 'لوحة ذكاء العقار')

@section('content')
<div class="container mx-auto py-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">عقارات مبالغ في سعرها</div>
            <div class="text-3xl font-bold">{{ $stats['overpriced'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">مقيدة تلقائياً</div>
            <div class="text-3xl font-bold">{{ $stats['restricted'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">تحتاج مراجعة</div>
            <div class="text-3xl font-bold">{{ $stats['needs_review'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">تم حساب الاستثمار</div>
            <div class="text-3xl font-bold">{{ $stats['scored'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">متوسط الأيام في السوق</div>
            <div class="text-3xl font-bold">{{ round($avgDaysOnMarket ?? 0, 1) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">الطلب مقابل العرض</div>
            <div class="text-3xl font-bold">{{ $demandSupply['ratio'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 mt-1">
                طلب: {{ $demandSupply['demand'] ?? 0 }} • عرض: {{ $demandSupply['supply'] ?? 0 }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">أسرع مناطق البيع</div>
            <ul class="text-sm mt-2 space-y-1">
                @foreach(($fastestAreas ?? collect([])) as $row)
                    <li class="flex justify-between">
                        <span>{{ $row->city }}</span>
                        <span class="text-slate-600">{{ number_format($row->avg_days, 1) }} يوم</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">أعلى العقارات مخاطرة (احتيال)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-right">
                <thead>
                    <tr class="text-slate-600 border-b">
                        <th class="py-2 pr-2">#</th>
                        <th class="py-2">العقار</th>
                        <th class="py-2">المدينة</th>
                        <th class="py-2">السعر</th>
                        <th class="py-2">درجة الاحتيال</th>
                        <th class="py-2">المستوى</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRisk as $p)
                        <tr class="border-b">
                            <td class="py-2 pr-2">{{ $p->id }}</td>
                            <td class="py-2">{{ $p->title }}</td>
                            <td class="py-2">{{ $p->city }}</td>
                            <td class="py-2">{{ number_format($p->price, 2) }}</td>
                            <td class="py-2">{{ $p->fraud_score }}</td>
                            <td class="py-2">{{ strtoupper($p->fraud_risk_level) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 text-center" colspan="6">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">خريطة طبقات الذكاء</h2>
            <div class="flex gap-2">
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="price" checked>سعر</label>
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="demand" checked>الطلب</label>
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="growth">النمو</label>
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="schools">المدارس</label>
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="transport">النقل</label>
                <label class="inline-flex items-center gap-1 text-sm"><input type="checkbox" class="layer-toggle" value="crime">الجريمة</label>
            </div>
        </div>
        <div id="intelligence-map" class="w-full h-[520px] rounded-xl border border-gray-200"></div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR8BzU=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('intelligence-map').setView([23.8859, 45.0792], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    const layers = {
        price: L.layerGroup().addTo(map),
        demand: L.layerGroup().addTo(map),
        growth: L.layerGroup(),
        schools: L.layerGroup(),
        transport: L.layerGroup(),
        crime: L.layerGroup()
    };

    function drawGrid(group, items, valueKey, color) {
        group.clearLayers();
        items.forEach(cell => {
            const rect = L.rectangle([
                [cell.lat, cell.lng],
                [cell.lat + 0.05, cell.lng + 0.05]
            ], {
                color: color,
                weight: 1,
                fillColor: color,
                fillOpacity: 0.25
            });
            rect.bindTooltip(`${valueKey}: ${cell[valueKey] ?? '-'}`);
            rect.addTo(group);
        });
    }

    async function fetchLayer(url) {
        const res = await fetch(url);
        if (!res.ok) return null;
        return res.json();
    }

    async function loadLayers() {
        const [price, demand, growth, schools, transport, crime] = await Promise.all([
            fetchLayer('/api/intelligence/layers/price-heatmap'),
            fetchLayer('/api/intelligence/layers/demand-density'),
            fetchLayer('/api/intelligence/layers/growth-zones'),
            fetchLayer('/api/intelligence/layers/schools'),
            fetchLayer('/api/intelligence/layers/transport'),
            fetchLayer('/api/intelligence/layers/crime')
        ]);
        if (price?.data) drawGrid(layers.price, price.data, 'avg_price', '#1d4ed8');
        if (demand?.data) drawGrid(layers.demand, demand.data, 'demand_score', '#059669');
        if (growth?.data) drawGrid(layers.growth, growth.data, 'growth_rate', '#f59e0b');
        if (schools?.data) drawGrid(layers.schools, schools.data, 'schools_coverage', '#8b5cf6');
        if (transport?.data) drawGrid(layers.transport, transport.data, 'transport_coverage', '#0ea5e9');
        if (crime?.data) drawGrid(layers.crime, crime.data, 'crime_index', '#ef4444');
    }

    loadLayers();

    document.querySelectorAll('.layer-toggle').forEach(cb => {
        cb.addEventListener('change', () => {
            const key = cb.value;
            if (cb.checked) {
                layers[key].addTo(map);
            } else {
                map.removeLayer(layers[key]);
            }
        });
    });
});
</script>
@endsection
