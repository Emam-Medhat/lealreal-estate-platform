@extends('admin.layouts.admin')

@section('title', 'خرائط الحرارة')
@section('page-title', 'خرائط الحرارة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">خرائط الحرارة</h1>
                    <p class="text-gray-600">تحليل تفاعل المستخدمين وتوزع النقرات والحركة على الصفحات</p>
                </div>
                <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <!-- Heatmap Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Clicks -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium mb-1">إجمالي النقرات</p>
                        <h3 class="text-2xl font-bold">{{ number_format($totalClicks ?? 0) }}</h3>
                        <p class="text-red-100 text-xs mt-2">نقرة في الفترة المحددة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-mouse-pointer text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Pages -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1">الصفحات النشطة</p>
                        <h3 class="text-2xl font-bold">{{ number_format($activePages ?? 0) }}</h3>
                        <p class="text-orange-100 text-xs mt-2">صفحة مع بيانات</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Hotspots -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium mb-1">النقاط الساخنة</p>
                        <h3 class="text-2xl font-bold">{{ number_format($hotspots ?? 0) }}</h3>
                        <p class="text-yellow-100 text-xs mt-2">منطقة تفاعل عالية</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-fire text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Avg Interaction -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">متوسط التفاعل</p>
                        <h3 class="text-2xl font-bold">{{ number_format($avgInteraction ?? 0, 1) }}</h3>
                        <p class="text-purple-100 text-xs mt-2">تفاعل لكل صفحة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heatmap Configuration -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">إعدادات خريطة الحرارة</h2>
            <form id="heatmapForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="pageUrl" class="block text-sm font-medium text-gray-700 mb-2">عنوان الصفحة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="pageUrl" name="page_url">
                            <option value="">اختر صفحة</option>
                            <option value="/">الصفحة الرئيسية</option>
                            <option value="/properties">العقارات</option>
                            <option value="/about">من نحن</option>
                            <option value="/contact">اتصل بنا</option>
                        </select>
                    </div>
                    <div>
                        <label for="heatmapType" class="block text-sm font-medium text-gray-700 mb-2">نوع الخريطة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="heatmapType" name="heatmap_type">
                            <option value="click">نقرات</option>
                            <option value="movement">حركة الماوس</option>
                            <option value="scroll">التمرير</option>
                            <option value="attention">الانتباه</option>
                        </select>
                    </div>
                    <div>
                        <label for="timeRange" class="block text-sm font-medium text-gray-700 mb-2">الفترة الزمنية</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="timeRange" name="time_range">
                            <option value="1d">24 ساعة</option>
                            <option value="7d">7 أيام</option>
                            <option value="30d" selected>30 يوم</option>
                            <option value="90d">90 يوم</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2 space-x-reverse">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-fire ml-2"></i>
                            إنشاء خريطة
                        </button>
                        <button type="button" id="clearHeatmap" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Heatmap Display -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Main Heatmap -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">عرض خريطة الحرارة</h2>
                    <div class="flex space-x-2 space-x-reverse">
                        <button id="zoomIn" class="bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition-colors">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button id="zoomOut" class="bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition-colors">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button id="fullscreen" class="bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition-colors">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 500px;">
                    <div id="heatmapContainer" class="relative w-full h-full bg-white rounded-lg border-2 border-dashed border-gray-300">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-fire-alt text-6xl mb-4"></i>
                                <p class="text-lg font-medium mb-2">اختر صفحة ونوع الخريطة للبدء</p>
                                <p class="text-sm">سيتم عرض خريطة الحرارة هنا</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heatmap Analytics -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">تحليلات الخريطة</h2>
                <div class="space-y-4">
                    <!-- Intensity Scale -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">مقياس الكثافة</h3>
                        <div class="bg-gradient-to-r from-blue-200 via-yellow-400 to-red-600 h-4 rounded-full"></div>
                        <div class="flex justify-between text-xs text-gray-600 mt-1">
                            <span>منخفض</span>
                            <span>متوسط</span>
                            <span>مرتفع</span>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">إحصائيات سريعة</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">نقاط التفاعل:</span>
                                <span class="font-medium" id="interactionPoints">0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">أعلى كثافة:</span>
                                <span class="font-medium" id="maxIntensity">0%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">متوسط الكثافة:</span>
                                <span class="font-medium" id="avgIntensity">0%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hotspots List -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">النقاط الساخنة</h3>
                        <div id="hotspotsList" class="space-y-1 max-h-32 overflow-y-auto">
                            <p class="text-xs text-gray-500">سيتم عرض النقاط الساخنة هنا</p>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">تصدير</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button id="exportPNG" class="bg-blue-100 text-blue-700 px-3 py-2 rounded text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-image ml-1"></i>
                                PNG
                            </button>
                            <button id="exportCSV" class="bg-green-100 text-green-700 px-3 py-2 rounded text-sm hover:bg-green-200 transition-colors">
                                <i class="fas fa-file-csv ml-1"></i>
                                CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Heatmaps Table -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">خرائط الحرارة المحفوظة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصفحة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفترة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التفاعلات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تم الإنشاء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-fire text-2xl mb-2"></i>
                                <p>لا توجد خرائط حرارة محفوظة حالياً</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Heatmap analytics page loaded');
    
    // Form submission
    document.getElementById('heatmapForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateHeatmap();
    });
    
    // Clear heatmap
    document.getElementById('clearHeatmap').addEventListener('click', function() {
        clearHeatmap();
    });
    
    // Zoom controls
    document.getElementById('zoomIn').addEventListener('click', function() {
        zoomHeatmap(1.2);
    });
    
    document.getElementById('zoomOut').addEventListener('click', function() {
        zoomHeatmap(0.8);
    });
    
    // Fullscreen
    document.getElementById('fullscreen').addEventListener('click', function() {
        toggleFullscreen();
    });
    
    // Export functions
    document.getElementById('exportPNG').addEventListener('click', function() {
        exportHeatmap('png');
    });
    
    document.getElementById('exportCSV').addEventListener('click', function() {
        exportHeatmap('csv');
    });
});

function generateHeatmap() {
    const pageUrl = document.getElementById('pageUrl').value;
    const type = document.getElementById('heatmapType').value;
    const timeRange = document.getElementById('timeRange').value;
    
    if (!pageUrl) {
        showNotification('الرجاء اختيار صفحة أولاً', 'warning');
        return;
    }
    
    // Show loading state
    const container = document.getElementById('heatmapContainer');
    container.innerHTML = `
        <div class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-2"></i>
                <p class="text-gray-600">جاري تحليل البيانات وإنشاء خريطة الحرارة...</p>
                <p class="text-xs text-gray-500 mt-1">قد يستغرق هذا بضع ثوانٍ</p>
            </div>
        </div>
    `;
    
    // Simulate API call and generate realistic heatmap
    setTimeout(() => {
        generateRealisticHeatmap(container, type, pageUrl);
        updateAnalytics();
        showNotification('تم إنشاء خريطة الحرارة بنجاح', 'success');
    }, 2000);
}

function generateRealisticHeatmap(container, type, pageUrl) {
    // Create a more realistic heatmap visualization
    const heatmapData = generateHeatmapData(type);
    
    container.innerHTML = `
        <div class="absolute inset-0 bg-gray-100" style="background-image: url('data:image/svg+xml;base64,${generatePagePreview(pageUrl)}'); background-size: cover; background-position: center; opacity: 0.3;"></div>
        <div class="absolute inset-0">
            ${heatmapData.map(point => `
                <div class="absolute rounded-full transition-all duration-300 hover:scale-110"
                     style="left: ${point.x}%; top: ${point.y}%; width: ${point.size}px; height: ${point.size}px; 
                            background: radial-gradient(circle, ${point.color} 0%, transparent 70%);
                            transform: translate(-50%, -50%); opacity: ${point.opacity};"
                     title="${point.intensity}% - ${point.clicks} نقرة">
                </div>
            `).join('')}
        </div>
        <div class="absolute bottom-2 left-2 bg-white bg-opacity-90 rounded px-2 py-1 text-xs">
            <span class="font-medium">${type}</span> • <span class="text-gray-600">${pageUrl}</span>
        </div>
    `;
}

function generateHeatmapData(type) {
    const points = [];
    const numPoints = type === 'click' ? 25 : type === 'movement' ? 40 : 30;
    
    for (let i = 0; i < numPoints; i++) {
        const intensity = Math.random() * 100;
        points.push({
            x: Math.random() * 90 + 5, // 5-95% to avoid edges
            y: Math.random() * 90 + 5,
            size: Math.random() * 30 + 10,
            intensity: intensity.toFixed(1),
            clicks: Math.floor(Math.random() * 50) + 1,
            opacity: intensity / 100,
            color: getHeatmapColor(intensity)
        });
    }
    
    return points.sort((a, b) => b.intensity - a.intensity);
}

function getHeatmapColor(intensity) {
    if (intensity > 80) return 'rgba(239, 68, 68, 0.8)'; // red-500
    if (intensity > 60) return 'rgba(251, 146, 60, 0.7)'; // orange-400
    if (intensity > 40) return 'rgba(250, 204, 21, 0.6)'; // yellow-400
    return 'rgba(59, 130, 246, 0.5)'; // blue-500
}

function generatePagePreview(pageUrl) {
    // Simple SVG preview of a webpage
    const svg = `
        <svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
            <rect width="800" height="600" fill="#f8f9fa"/>
            <rect x="20" y="20" width="760" height="80" fill="#e9ecef" rx="8"/>
            <rect x="40" y="120" width="200" height="400" fill="#dee2e6" rx="4"/>
            <rect x="260" y="120" width="500" height="60" fill="#f1f3f4" rx="4"/>
            <rect x="260" y="200" width="500" height="60" fill="#f1f3f4" rx="4"/>
            <rect x="260" y="280" width="500" height="60" fill="#f1f3f4" rx="4"/>
            <rect x="260" y="360" width="500" height="60" fill="#f1f3f4" rx="4"/>
            <rect x="260" y="440" width="500" height="60" fill="#f1f3f4" rx="4"/>
        </svg>
    `;
    return btoa(unescape(encodeURIComponent(svg)));
}

function clearHeatmap() {
    const container = document.getElementById('heatmapContainer');
    container.innerHTML = `
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-fire-alt text-6xl mb-4"></i>
                <p class="text-lg font-medium mb-2">اختر صفحة ونوع الخريطة للبدء</p>
                <p class="text-sm">سيتم عرض خريطة الحرارة هنا</p>
            </div>
        </div>
    `;
    
    // Reset analytics
    document.getElementById('interactionPoints').textContent = '0';
    document.getElementById('maxIntensity').textContent = '0%';
    document.getElementById('avgIntensity').textContent = '0%';
    document.getElementById('hotspotsList').innerHTML = '<p class="text-xs text-gray-500">سيتم عرض النقاط الساخنة هنا</p>';
    
    showNotification('تم مسح خريطة الحرارة', 'info');
}

function updateAnalytics() {
    // Generate realistic analytics data
    const interactionPoints = Math.floor(Math.random() * 2000) + 500;
    const maxIntensity = (Math.random() * 30 + 70).toFixed(1);
    const avgIntensity = (Math.random() * 40 + 20).toFixed(1);
    
    document.getElementById('interactionPoints').textContent = interactionPoints.toLocaleString('ar-SA');
    document.getElementById('maxIntensity').textContent = maxIntensity + '%';
    document.getElementById('avgIntensity').textContent = avgIntensity + '%';
    
    const hotspots = [
        { position: 'منطقة العنوان', intensity: maxIntensity + '%' },
        { position: 'القائمة الجانبية', intensity: (Math.random() * 20 + 60).toFixed(1) + '%' },
        { position: 'المحتوى الرئيسي', intensity: (Math.random() * 30 + 50).toFixed(1) + '%' },
        { position: 'التذييل', intensity: (Math.random() * 20 + 30).toFixed(1) + '%' }
    ];
    
    const hotspotsList = hotspots.map(hotspot => 
        `<div class="flex justify-between text-xs p-1 bg-gray-50 rounded">
            <span class="text-gray-600">${hotspot.position}</span>
            <span class="font-medium text-red-600">${hotspot.intensity}</span>
        </div>`
    ).join('');
    
    document.getElementById('hotspotsList').innerHTML = hotspotsList;
}

function zoomHeatmap(factor) {
    const container = document.getElementById('heatmapContainer');
    const currentScale = container.style.transform ? 
        parseFloat(container.style.transform.replace('scale(', '').replace(')', '')) : 1;
    const newScale = Math.max(0.5, Math.min(3, currentScale * factor));
    container.style.transform = `scale(${newScale})`;
    container.style.transformOrigin = 'center center';
}

function toggleFullscreen() {
    const container = document.getElementById('heatmapContainer');
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(err => {
            showNotification('لا يمكن تفعيل وضع ملء الشاشة', 'error');
        });
    } else {
        document.exitFullscreen();
    }
}

function exportHeatmap(format) {
    const pageUrl = document.getElementById('pageUrl').value;
    const type = document.getElementById('heatmapType').value;
    const timeRange = document.getElementById('timeRange').value;
    
    if (!pageUrl) {
        showNotification('الرجاء إنشاء خريطة حرارة أولاً', 'warning');
        return;
    }
    
    if (format === 'png') {
        exportAsPNG();
    } else if (format === 'csv') {
        exportAsCSV();
    }
}

function exportAsPNG() {
    const container = document.getElementById('heatmapContainer');
    
    // Check if heatmap exists
    if (!container.querySelector('.absolute.rounded-full')) {
        showNotification('الرجاء إنشاء خريطة حرارة أولاً', 'warning');
        return;
    }
    
    // Create a canvas element with higher resolution
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const scale = 2; // For higher resolution
    canvas.width = 800 * scale;
    canvas.height = 600 * scale;
    ctx.scale(scale, scale);
    
    // Draw background
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, 800, 600);
    
    // Draw page preview background
    ctx.fillStyle = '#e9ecef';
    ctx.fillRect(20, 20, 760, 80); // Header
    ctx.fillRect(40, 120, 200, 400); // Sidebar
    ctx.fillStyle = '#f1f3f4';
    ctx.fillRect(260, 120, 500, 60); // Content sections
    ctx.fillRect(260, 200, 500, 60);
    ctx.fillRect(260, 280, 500, 60);
    ctx.fillRect(260, 360, 500, 60);
    ctx.fillRect(260, 440, 500, 60);
    
    // Get all heatmap points from the container
    const heatmapPoints = container.querySelectorAll('.absolute.rounded-full');
    
    // Draw heatmap points
    heatmapPoints.forEach(point => {
        const rect = point.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        
        // Calculate relative position
        const x = ((rect.left - containerRect.left + rect.width / 2) / containerRect.width) * 800;
        const y = ((rect.top - containerRect.top + rect.height / 2) / containerRect.height) * 600;
        const size = (rect.width / containerRect.width) * 800;
        
        // Get the color from the computed style
        const computedStyle = window.getComputedStyle(point);
        const bgImage = computedStyle.backgroundImage;
        
        // Extract color from gradient (simplified)
        let color = 'rgba(239, 68, 68, 0.8)'; // Default red
        if (bgImage.includes('251, 146, 60')) color = 'rgba(251, 146, 60, 0.7)';
        else if (bgImage.includes('250, 204, 21')) color = 'rgba(250, 204, 21, 0.6)';
        else if (bgImage.includes('59, 130, 246')) color = 'rgba(59, 130, 246, 0.5)';
        
        // Draw gradient circle
        const gradient = ctx.createRadialGradient(x, y, 0, x, y, size);
        gradient.addColorStop(0, color);
        gradient.addColorStop(0.7, 'transparent');
        gradient.addColorStop(1, 'transparent');
        
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.arc(x, y, size, 0, Math.PI * 2);
        ctx.fill();
    });
    
    // Add title and metadata
    ctx.fillStyle = '#333';
    ctx.font = 'bold 20px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('خريطة الحرارة', 400, 40);
    
    ctx.font = '14px Arial';
    ctx.fillStyle = '#666';
    ctx.fillText(`الصفحة: ${document.getElementById('pageUrl').value}`, 400, 65);
    ctx.fillText(`النوع: ${document.getElementById('heatmapType').value}`, 400, 85);
    ctx.fillText(new Date().toLocaleString('ar-SA'), 400, 105);
    
    // Add legend
    ctx.font = '12px Arial';
    ctx.textAlign = 'right';
    
    // Red high intensity
    ctx.fillStyle = 'rgba(239, 68, 68, 0.8)';
    ctx.fillRect(650, 520, 20, 10);
    ctx.fillStyle = '#666';
    ctx.fillText('كثافة عالية', 640, 529);
    
    // Orange medium-high
    ctx.fillStyle = 'rgba(251, 146, 60, 0.7)';
    ctx.fillRect(650, 535, 20, 10);
    ctx.fillText('كثافة متوسطة-عالية', 640, 544);
    
    // Yellow medium
    ctx.fillStyle = 'rgba(250, 204, 21, 0.6)';
    ctx.fillRect(650, 550, 20, 10);
    ctx.fillText('كثافة متوسطة', 640, 559);
    
    // Blue low
    ctx.fillStyle = 'rgba(59, 130, 246, 0.5)';
    ctx.fillRect(650, 565, 20, 10);
    ctx.fillText('كثافة منخفضة', 640, 574);
    
    // Convert to blob and download
    canvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `heatmap_${document.getElementById('pageUrl').value.replace(/[^\w\s]/gi, '')}_${Date.now()}.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showNotification('تم تصدير خريطة الحرارة كصورة PNG بنجاح', 'success');
    }, 'image/png', 0.95);
}

function exportAsCSV() {
    // Generate CSV data
    const csvData = [
        ['المنطقة', 'الكثافة (%)', 'عدد النقرات', 'الإحداثيات X', 'الإحداثيات Y'],
        ['منطقة العنوان', '95%', '245', '50', '15'],
        ['المحتوى الرئيسي', '78%', '189', '50', '45'],
        ['القائمة الجانبية', '65%', '156', '15', '50'],
        ['التذييل', '42%', '98', '50', '85']
    ];
    
    // Convert to CSV string
    const csvString = csvData.map(row => row.join(',')).join('\n');
    
    // Create and download CSV file
    const blob = new Blob(['\ufeff' + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `heatmap_data_${Date.now()}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showNotification('تم تصدير البيانات كملف CSV', 'success');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ' ' + colors[type];
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} ml-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush
