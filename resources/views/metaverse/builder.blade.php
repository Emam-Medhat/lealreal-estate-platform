@extends('layouts.app')

@section('title', 'منشئ العقارات الافتراضية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">منشئ العقارات الافتراضية</h1>
        <p class="text-xl opacity-90">صممم وبنِ العقارات في العوالم الافتراضية</p>
        
        <!-- Builder Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_designs'] }}</div>
                <div class="text-sm opacity-90">التصاميمات</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['active_builds'] }}</div>
                <div class="text-sm opacity-90">بناء نشط</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['completed_builds'] }}</div>
                <div class="text-sm opacity-90">مكتملة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_downloads'] }}</div>
                <div class="text-sm opacity-90">التحميلات</div>
            </div>
        </div>
    </div>

    <!-- Builder Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Panel - Design Selection -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Design Selection -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">اختر التصميم</h2>
                
                <!-- Search -->
                <div class="mb-4">
                    <input type="text" id="design-search" placeholder="البحث عن التصاميمات..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                
                <!-- Filters -->
                <div class="space-y-3 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع التصميم</label>
                        <select id="design-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">كل الأنواع</option>
                            <option value="residential">سكني</option>
                            <option value="commercial">تجاري</option>
                            <option value="mixed">مختلط</option>
                            <option value="industrial">صناعي</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">النمط المعماري</label>
                        <select id="style-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">كل الأنماط</option>
                            <option value="modern">حديث</option>
                            <option value="classical">كلاسيكي</option>
                            <option value="minimalist">بسيط</option>
                            <option value="industrial">صناعي</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">مستوى الصعوبة</label>
                        <select id="difficulty-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">كل المستويات</option>
                            <option value="beginner">مبتدئ</option>
                            <option value="intermediate">متوسط</option>
                            <option value="advanced">متقدم</option>
                            <option value="expert">خبير</option>
                        </select>
                    </div>
                </div>
                
                <!-- Design List -->
                <div id="design-list" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($designs as $design)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer design-item" 
                             data-design-id="{{ $design->id }}">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-sm">{{ $design->title }}</h3>
                                <div class="text-xs text-gray-500">{{ $design->getDifficultyLevelTextAttribute() }}</div>
                            </div>
                            <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $design->description }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $design->getArchitecturalStyleTextAttribute() }}</span>
                                <span>{{ $design->getFormattedCostAttribute() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div>لم يتم العثور على تصاميمات</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">إجراءات سريعة</h2>
                <div class="space-y-2">
                    <button onclick="createNewDesign()" 
                            class="w-full bg-teal-600 text-white py-2 px-4 rounded-md hover:bg-teal-700 transition-colors">
                        🎨 تصميم جديد
                    </button>
                    <button onclick="importDesign()" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        📥 استيراد تصميم
                    </button>
                    <button onclick="saveWorkspace()" 
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                        💾 حفظ مساحة العمل
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Builder Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Builder Toolbar -->
            <div class="bg-white rounded-lg shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="select-tool" class="tool-btn bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            🖱️ تحديد
                        </button>
                        <button id="move-tool" class="tool-btn bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            ✋️ نقل
                        </button>
                        <button id="rotate-tool" class="tool-btn bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            🔄 تدوير
                        </button>
                        <button id="scale-tool" class="tool-btn bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            🔍 تكبير
                        </button>
                        <button id="text-tool" class="tool-btn bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            📝 نص
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button onclick="undoAction()" 
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            ↶️ تراجع
                        </button>
                        <button onclick="redoAction()" 
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            ↪️ إعادة
                        </button>
                        <button onclick="clearCanvas()" 
                                class="bg-red-200 text-red-700 px-3 py-2 rounded-md hover:bg-red-300 transition-colors">
                            🗑️ مسح
                        </button>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold">منطقة البناء</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">التصميم: <span id="current-design-name">غير محدد</span></span>
                        <span class="text-sm text-gray-500">الأرض: <span id="current-land-name">غير محدد</span></span>
                    </div>
                </div>
                
                <!-- 3D Canvas Placeholder -->
                <div id="builder-canvas" class="bg-gray-100 rounded-lg h-96 flex items-center justify-center border-2 border-dashed border-gray-300">
                    <div class="text-center">
                        <div class="text-6xl mb-4">🏗️</div>
                        <h3 class="text-xl font-bold mb-2">منشئ العقارات الافتراضية</h3>
                        <p class="text-gray-600 mb-4">اختر تصميم أو ابدأ من الصفر</p>
                        <button onclick="startBuilding()" 
                                class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700 transition-colors">
                            ابدأ البناء
                        </button>
                    </div>
                </div>
                
                <!-- Properties Panel -->
                <div id="properties-panel" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                    <h3 class="font-semibold mb-3">خصائص العنصر</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">العرض</label>
                            <input type="number" id="prop-width" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الارتفاع</label>
                            <input type="number" id="prop-height" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">العمق</label>
                            <input type="number" id="prop-depth" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المادة</label>
                            <select id="prop-material" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="concrete">خرسانة</option>
                                <option value="wood">خشب</option>
                                <option value="glass">زجاج</option>
                                <option value="metal">معدن</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button onclick="applyProperties()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            تطبيق
                        </button>
                    </div>
                </div>
            </div>

            <!-- Layer Management -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">إدارة الطبقات</h2>
                <div id="layers-list" class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">الطبقة الأساسية</span>
                            <span class="text-xs text-gray-500">قفل</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-gray-500 hover:text-gray-700">👁️</button>
                            <button class="text-gray-500 hover:text-gray-700">👁️</button>
                        </div>
                    </div>
                </div>
                <button onclick="addLayer()" 
                        class="mt-4 bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 transition-colors">
                    + إضافة طبقة
                </button>
            </div>

            <!-- Asset Library -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">مكتبة الأصول</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🏠️</div>
                        <div class="text-sm">الجدران</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🪟</div>
                        <div class="text-sm">النوافذ</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🪑</div>
                        <div class="text-sm">الأبواب</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🪟</div>
                        <div class="text-sm">النوافذ</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🪑</div>
                        <div class="text-sm">الأبواب</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="text-4xl mb-2">🪑</div>
                        <div class="text-sm">الأبواب</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="progress-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">تقدم البناء</h3>
            <div class="mb-4">
                <div class="bg-gray-200 rounded-full h-2 mb-2">
                    <div id="progress-bar" class="bg-teal-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="text-sm text-gray-600 text-center">
                    <span id="progress-text">جاري التحضير...</span>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="closeProgressModal()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Builder functionality
let currentDesign = null;
let currentLand = null;
let selectedTool = null;
let buildingHistory = [];
let historyIndex = -1;

// Initialize builder
document.addEventListener('DOMContentLoaded', function() {
    initializeBuilder();
});

function initializeBuilder() {
    // Setup tool selection
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectTool(this.id.replace('-tool', ''));
        });
    });

    // Setup design selection
    document.querySelectorAll('.design-item').forEach(item => {
        item.addEventListener('click', function() {
            selectDesign(this.dataset.designId);
        });
    });

    // Setup search and filters
    document.getElementById('design-search').addEventListener('input', filterDesigns);
    document.getElementById('design-type-filter').addEventListener('change', filterDesigns);
    document.getElementById('style-filter').addEventListener('change', filterDesigns);
    document.getElementById('difficulty-filter').addEventListener('change', filterDesigns);
}

function selectTool(toolName) {
    selectedTool = toolName;
    
    // Update UI
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const selectedBtn = document.getElementById(toolName + '-tool');
    selectedBtn.classList.remove('bg-gray-200', 'text-gray-700');
    selectedBtn.classList.add('bg-teal-600', 'text-white');
    
    // Update cursor
    const canvas = document.getElementById('builder-canvas');
    canvas.style.cursor = getCursorForTool(toolName);
}

function getCursorForTool(tool) {
    const cursors = {
        'select': 'default',
        'move': 'move',
        'rotate': 'grab',
        'scale': 'nwse-resize',
        'text': 'text'
    };
    return cursors[tool] || 'default';
}

function selectDesign(designId) {
    // Load design data
    fetch(`/api/metaverse/designs/${designId}`)
        .then(response => response.json())
        .then(data => {
            currentDesign = data;
            document.getElementById('current-design-name').textContent = data.title;
            
            // Load design into canvas
            loadDesignIntoCanvas(data);
            
            // Show properties panel
            document.getElementById('properties-panel').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading design:', error);
            alert('فشل تحميل التصميم');
        });
}

function loadDesignIntoCanvas(design) {
    // This would integrate with a 3D rendering library
    const canvas = document.getElementById('builder-canvas');
    canvas.innerHTML = `
        <div class="text-center">
            <div class="text-6xl mb-4">🏗️</div>
            <h3 class="text-xl font-bold mb-2">${design.title}</h3>
            <p class="text-gray-600 mb-4">جاري تحميل التصميم...</p>
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
        </div>
    `;
    
    // Simulate loading
    setTimeout(() => {
        canvas.innerHTML = `
            <div class="text-center">
                <div class="text-6xl mb-4">🏗️</div>
                <h3 class="text-xl font-bold mb-2">${design.title}</h3>
                <p class="text-gray-600 mb-4">التصميم جاهز للبناء</p>
                <div class="bg-gray-200 rounded-lg h-32 flex items-center justify-center">
                    <div class="text-gray-500">منطقة البناء</div>
                </div>
            </div>
        `;
    }, 2000);
}

function filterDesigns() {
    const searchTerm = document.getElementById('design-search').value.toLowerCase();
    const typeFilter = document.getElementById('design-type-filter').value;
    const styleFilter = document.getElementById('style-filter').value;
    const difficultyFilter = document.getElementById('difficulty-filter').value;
    
    document.querySelectorAll('.design-item').forEach(item => {
        const title = item.querySelector('h3').textContent.toLowerCase();
        const description = item.querySelector('p').textContent.toLowerCase();
        const type = item.querySelector('.text-xs').textContent;
        
        let visible = true;
        
        if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
            visible = false;
        }
        
        if (typeFilter && !type.includes(typeFilter)) {
            visible = false;
        }
        
        item.style.display = visible ? 'block' : 'none';
    });
}

function createNewDesign() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">إنشاء تصميم جديد</h3>
            <form id="new-design-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع التصميم</label>
                    <select name="design_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="residential">سكني</option>
                        <option value="commercial">تجاري</option>
                        <option value="mixed">مختلط</option>
                        <option value="industrial">صناعي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النمط المعماري</label>
                    <select name="architectural_style" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="modern">حديث</option>
                        <option value="classical">كلاسيكي</option>
                        <option value="minimalist">بسيط</option>
                        <option value="industrial">صناعي</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-md hover:bg-teal-700 transition-colors">
                        إنشاء
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('new-design-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch('/api/metaverse/designs', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title: formData.get('title'),
                description: formData.get('description'),
                design_type: formData.get('design_type'),
                architectural_style: formData.get('architectural_style'),
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                alert('تم إنشاء التصميم بنجاح!');
                // Reload designs list
                location.reload();
            } else {
                alert(data.message || 'فشل إنشاء التصميم');
            }
        })
        .catch(error => {
            console.error('Error creating design:', error);
            alert('حدث خطأ أثناء إنشاء التصميم');
        });
    });
}

function startBuilding() {
    if (!currentDesign) {
        alert('يرجى اختيار تصميم أولاً');
        return;
    }
    
    if (!currentLand) {
        selectLand();
        return;
    }
    
    // Show progress modal
    const modal = document.getElementById('progress-modal');
    modal.classList.remove('hidden');
    
    // Simulate building process
    simulateBuilding();
}

function selectLand() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">اختر الأرض</h3>
            <div class="space-y-2">
                @foreach($lands as $land)
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer land-option" 
                         data-land-id="{{ $land->id }}">
                        <div class="font-semibold">{{ $land->title }}</div>
                        <div class="text-sm text-gray-500">{{ $land->getFormattedAreaAttribute() }} - {{ $land->getFormattedPriceAttribute() }}</div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    إلغاء
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle land selection
    document.querySelectorAll('.land-option').forEach(option => {
        option.addEventListener('click', function() {
            currentLand = this.dataset.landId;
            const landName = this.querySelector('.font-semibold').textContent;
            document.getElementById('current-land-name').textContent = landName;
            closeModal();
            startBuilding();
        });
    });
}

function simulateBuilding() {
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    const steps = [
        { progress: 20, text: 'تحليل التصميم...' },
        { progress: 40, text: 'تحضين المواد...' },
        { progress: 60, text: 'بناء الهيكل...' },
        { progress: 80, text: 'تطبيق المواد...' },
        { progress: 100, text: 'اكتمل!' }
    ];
    
    let currentStep = 0;
    
    const interval = setInterval(() => {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            progressBar.style.width = step.progress + '%';
            progressText.textContent = step.text;
            currentStep++;
        } else {
            clearInterval(interval);
            setTimeout(() => {
                closeProgressModal();
                alert('تم بناء العقار بنجاح!');
            }, 1000);
        }
    }, 1000);
}

function closeProgressModal() {
    document.getElementById('progress-modal').classList.add('hidden');
}

function closeModal() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

function undoAction() {
    if (historyIndex > 0) {
        historyIndex--;
        // Restore previous state
        console.log('Undo action');
    }
}

function redoAction() {
    if (historyIndex < buildingHistory.length - 1) {
        historyIndex++;
        // Restore next state
        console.log('Redo action');
    }
}

function clearCanvas() {
    if (confirm('هل أنت متأكد من مسح اللوحة؟')) {
        const canvas = document.getElementById('builder-canvas');
        canvas.innerHTML = `
            <div class="text-center">
                <div class="text-6xl mb-4">🏗️</div>
                <h3 class="text-xl font-bold mb-2">منشئ العقارات الافتراضية</h3>
                <p class="text-gray-600 mb-4">اختر تصميم أو ابدأ من الصفر</p>
                <button onclick="startBuilding()" 
                        class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700 transition-colors">
                    ابدأ البناء
                </button>
            </div>
        `;
        
        // Clear history
        buildingHistory = [];
        historyIndex = -1;
    }
}

function applyProperties() {
    // Apply selected properties to current element
    const width = document.getElementById('prop-width').value;
    const height = document.getElementById('prop-height').value;
    const depth = document.getElementById('prop-depth').value;
    const material = document.getElementById('prop-material').value;
    
    console.log('Applying properties:', { width, height, depth, material });
    
    // This would update the 3D model
    alert('تم تطبيق الخصائص');
}

function addLayer() {
    const layersList = document.getElementById('layers-list');
    const layerCount = layersList.children.length;
    
    const newLayer = document.createElement('div');
    newLayer.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
    newLayer.innerHTML = `
        <div class="flex items-center space-x-2">
            <input type="text" value="الطبقة ${layerCount + 1}" class="text-sm font-medium bg-transparent border-0 outline-none">
            <span class="text-xs text-gray-500">نشط</span>
        </div>
        <div class="flex items-center space-x-2">
            <button class="text-gray-500 hover:text-gray-700">👁️</button>
            <button class="text-gray-500 hover:text-gray-700">👁️</button>
        </div>
    `;
    
    layersList.appendChild(newLayer);
}

// Real-time updates
setInterval(() => {
    // Update builder stats
    fetch('/api/metaverse/builder/stats')
        .then(response => response.json())
        .then(data => {
            console.log('Builder stats:', data);
        });
}, 30000); // Update every 30 seconds
</script>
@endsection
