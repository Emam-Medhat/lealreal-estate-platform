@extends('layouts.app')

@section('title', 'حاسبة الاستدامة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">حاسبة الاستدامة</h1>
                <a href="{{ route('sustainability.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calculator Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">حساب درجة الاستدامة</h6>
                </div>
                <div class="card-body">
                    <form id="sustainabilityCalculator" method="POST" action="{{ route('sustainability.calculator.calculate') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property">العقار</label>
                                    <select name="property_id" id="property" class="form-control" required>
                                        <option value="">اختر العقار</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}">{{ $property->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_type">نوع العقار</label>
                                    <select name="property_type" id="property_type" class="form-control" required>
                                        <option value="">اختر النوع</option>
                                        <option value="residential">سكني</option>
                                        <option value="commercial">تجاري</option>
                                        <option value="industrial">صناعي</option>
                                        <option value="mixed">مختلط</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Energy Section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">الطاقة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="energy_consumption">استهلاك الطاقة (كيلوواط/ساعة)</label>
                                            <input type="number" name="energy_consumption" id="energy_consumption" 
                                                   class="form-control" step="0.1" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="renewable_energy_percentage">نسبة الطاقة المتجددة (%)</label>
                                            <input type="number" name="renewable_energy_percentage" id="renewable_energy_percentage" 
                                                   class="form-control" step="0.1" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="insulation_rating">تقييم العزل (1-10)</label>
                                            <input type="number" name="insulation_rating" id="insulation_rating" 
                                                   class="form-control" min="1" max="10" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="solar_panels" id="solar_panels" class="form-check-input">
                                            <label for="solar_panels" class="form-check-label">ألواح شمسية</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="led_lighting" id="led_lighting" class="form-check-input">
                                            <label for="led_lighting" class="form-check-label">إضاءة LED</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="smart_thermostat" id="smart_thermostat" class="form-check-input">
                                            <label for="smart_thermostat" class="form-check-label">منظم حرارة ذكي</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="energy_star_appliances" id="energy_star_appliances" class="form-check-input">
                                            <label for="energy_star_appliances" class="form-check-label">أجهزة موفرة للطاقة</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="double_glazing" id="double_glazing" class="form-check-input">
                                            <label for="double_glazing" class="form-check-label">نوافذ مزدوجة</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="energy_monitoring" id="energy_monitoring" class="form-check-input">
                                            <label for="energy_monitoring" class="form-check-label">مراقبة استهلاك الطاقة</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Water Section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">المياه</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="water_consumption">استهلاك المياه (لتر/يوم)</label>
                                            <input type="number" name="water_consumption" id="water_consumption" 
                                                   class="form-control" step="0.1" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="rainwater_capacity">سعة تجميع مياه الأمطار (لتر)</label>
                                            <input type="number" name="rainwater_capacity" id="rainwater_capacity" 
                                                   class="form-control" step="0.1" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="greywater_capacity">سعة إعادة تدوير المياه (لتر)</label>
                                            <input type="number" name="greywater_capacity" id="greywater_capacity" 
                                                   class="form-control" step="0.1" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="rainwater_harvesting" id="rainwater_harvesting" class="form-check-input">
                                            <label for="rainwater_harvesting" class="form-check-label">تجميع مياه الأمطار</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="greywater_recycling" id="greywater_recycling" class="form-check-input">
                                            <label for="greywater_recycling" class="form-check-label">إعادة تدوير المياه</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="low_flow_fixtures" id="low_flow_fixtures" class="form-check-input">
                                            <label for="low_flow_fixtures" class="form-check-label">أجهزة منخفضة التدفق</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="smart_irrigation" id="smart_irrigation" class="form-check-input">
                                            <label for="smart_irrigation" class="form-check-label">ري ذكي</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="drip_irrigation" id="drip_irrigation" class="form-check-input">
                                            <label for="drip_irrigation" class="form-check-label">ري بالتنقيط</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="leak_detection" id="leak_detection" class="form-check-input">
                                            <label for="leak_detection" class="form-check-label">كشف التسربات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">المواد</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sustainable_materials_percentage">نسبة المواد المستدامة (%)</label>
                                            <input type="number" name="sustainable_materials_percentage" id="sustainable_materials_percentage" 
                                                   class="form-control" step="0.1" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="recycled_content_percentage">نسبة المحتوى المعاد تدويره (%)</label>
                                            <input type="number" name="recycled_content_percentage" id="recycled_content_percentage" 
                                                   class="form-control" step="0.1" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="local_materials_percentage">نسبة المواد المحلية (%)</label>
                                            <input type="number" name="local_materials_percentage" id="local_materials_percentage" 
                                                   class="form-control" step="0.1" min="0" max="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="recyclable_materials" id="recyclable_materials" class="form-check-input">
                                            <label for="recyclable_materials" class="form-check-label">مواد قابلة لإعادة التدوير</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="biodegradable_materials" id="biodegradable_materials" class="form-check-input">
                                            <label for="biodegradable_materials" class="form-check-label">مواد قابلة للتحلل</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="certified_materials" id="certified_materials" class="form-check-input">
                                            <label for="certified_materials" class="form-check-label">مواد معتمدة</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="low_voc_materials" id="low_voc_materials" class="form-check-input">
                                            <label for="low_voc_materials" class="form-check-label">مواد منخفضة المركبات العضوية المتطايرة</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="rapidly_renewable" id="rapidly_renewable" class="form-check-input">
                                            <label for="rapidly_renewable" class="form-check-label">مواد سريعة التجدد</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="salvaged_materials" id="salvaged_materials" class="form-check-input">
                                            <label for="salvaged_materials" class="form-check-label">مواد منقذة</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carbon Footprint Section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">البصمة الكربونية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="energy_footprint">بصمة الطاقة (كجم CO2/سنة)</label>
                                            <input type="number" name="energy_footprint" id="energy_footprint" 
                                                   class="form-control" step="0.1" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transport_footprint">بصمة النقل (كجم CO2/سنة)</label>
                                            <input type="number" name="transport_footprint" id="transport_footprint" 
                                                   class="form-control" step="0.1" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waste_footprint">بصمة النفايات (كجم CO2/سنة)</label>
                                            <input type="number" name="waste_footprint" id="waste_footprint" 
                                                   class="form-control" step="0.1" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Features -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">مميزات إضافية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="green_space_ratio">نسبة المساحات الخضراء (%)</label>
                                            <input type="number" name="green_space_ratio" id="green_space_ratio" 
                                                   class="form-control" step="0.1" min="0" max="100">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="green_roof" id="green_roof" class="form-check-input">
                                            <label for="green_roof" class="form-check-label">سقف أخضر</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="native_plants" id="native_plants" class="form-check-input">
                                            <label for="native_plants" class="form-check-label">نباتات محلية</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="permeable_surfaces" id="permeable_surfaces" class="form-check-input">
                                            <label for="permeable_surfaces" class="form-check-label">أسطح قابلة للنفاذ</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="waste_reduction_percentage">نسبة تقليل النفايات (%)</label>
                                            <input type="number" name="waste_reduction_percentage" id="waste_reduction_percentage" 
                                                   class="form-control" step="0.1" min="0" max="100">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="composting_system" id="composting_system" class="form-check-input">
                                            <label for="composting_system" class="form-check-label">نظام الكمبوست</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="recycling_program" id="recycling_program" class="form-check-input">
                                            <label for="recycling_program" class="form-check-label">برنامج إعادة التدوير</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="waste_monitoring" id="waste_monitoring" class="form-check-input">
                                            <label for="waste_monitoring" class="form-check-label">مراقبة النفايات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator"></i> حساب درجة الاستدامة
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="resetForm()">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="col-lg-4">
            <div class="card shadow mb-4" id="resultsPanel" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نتائج الحساب</h6>
                </div>
                <div class="card-body">
                    <div id="calculationResults">
                        <!-- Results will be loaded here via AJAX -->
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">نصائح لتحسين الاستدامة</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <h6 class="text-success">الطاقة</h6>
                            <ul class="small mb-0">
                                <li>استبدل الأجهزة القديمة بأجهزة موفرة للطاقة</li>
                                <li>ثبت ألواح شمسية لتوليد الطاقة النظيفة</li>
                                <li>حسن عزل المبنى لتقليل استهلاك التدفئة والتبريد</li>
                            </ul>
                        </div>
                        <div class="list-group-item">
                            <h6 class="text-info">المياه</h6>
                            <ul class="small mb-0">
                                <li>ثبت أجهزة منخفضة التدفق</li>
                                <li>جمع مياه الأمطار للاستخدام في الحدائق</li>
                                <li>أصلح التسربات فوراً</li>
                            </ul>
                        </div>
                        <div class="list-group-item">
                            <h6 class="text-warning">المواد</h6>
                            <ul class="small mb-0">
                                <li>استخدم مواد معاد تدويرها</li>
                                <li>اختر مواد محلية لتقليل البصمة الكربونية</li>
                                <li>ابحث عن شهادات الاستدامة للمواد</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetForm() {
        document.getElementById('sustainabilityCalculator').reset();
        document.getElementById('resultsPanel').style.display = 'none';
    }

    document.getElementById('sustainabilityCalculator').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحساب...';
        submitButton.disabled = true;
        
        fetch('{{ route("sustainability.calculator.calculate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.results);
                document.getElementById('resultsPanel').style.display = 'block';
                // Scroll to results
                document.getElementById('resultsPanel').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('حدث خطأ: ' + (data.message || 'يرجى المحاولة مرة أخرى'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.');
        })
        .finally(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });

    function displayResults(results) {
        const resultsHtml = `
            <div class="text-center mb-4">
                <h2 class="display-4 text-${results.overall_score >= 80 ? 'success' : (results.overall_score >= 60 ? 'warning' : 'danger')}">
                    ${results.overall_score.toFixed(1)}
                </h2>
                <p class="text-muted">درجة الاستدامة الإجمالية</p>
            </div>
            
            <div class="mb-4">
                <h6>تفصيل الدرجات</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>الطاقة</span>
                        <span>${results.energy_score.toFixed(1)}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: ${results.energy_score}%"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>المياه</span>
                        <span>${results.water_score.toFixed(1)}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: ${results.water_score}%"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>المواد</span>
                        <span>${results.materials_score.toFixed(1)}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: ${results.materials_score}%"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>البصمة الكربونية</span>
                        <span>${results.carbon_score.toFixed(1)}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: ${results.carbon_score}%"></div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <h6>التوصيات</h6>
                <ul class="small">
                    ${results.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                </ul>
            </div>
            
            <div class="mb-4">
                <h6>أهلية الشهادات</h6>
                <div>
                    ${results.certification_eligibility.map(cert => 
                        `<span class="badge badge-success mr-1">${cert}</span>`
                    ).join('')}
                </div>
            </div>
            
            <div class="text-center">
                <button onclick="saveResults()" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> حفظ النتائج
                </button>
                <button onclick="generateReport()" class="btn btn-success btn-sm ml-2">
                    <i class="fas fa-file-alt"></i> إنشاء تقرير
                </button>
            </div>
        `;
        
        document.getElementById('calculationResults').innerHTML = resultsHtml;
    }

    function saveResults() {
        // Implement save functionality
        alert('سيتم حفظ النتائج في قاعدة البيانات');
    }

    function generateReport() {
        // Implement report generation
        window.location.href = '{{ route("sustainability.reports.create") }}';
    }

    // Auto-calculate on field change (optional)
    document.querySelectorAll('#sustainabilityCalculator input, #sustainabilityCalculator select').forEach(element => {
        element.addEventListener('change', function() {
            // Optional: Auto-calculate when fields change
            // For better UX, you might want to add a delay or only calculate on specific fields
        });
    });
</script>
@endpush
