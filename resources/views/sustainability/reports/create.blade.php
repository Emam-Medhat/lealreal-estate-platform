@extends('layouts.app')

@section('title', 'إنشاء تقرير استدامة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">إنشاء تقرير استدامة</h1>
                <a href="{{ route('sustainability.reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة إلى التقارير
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('sustainability.reports.store') }}">
        @csrf
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">معلومات أساسية</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_sustainability_id">العقار</label>
                                    <select name="property_sustainability_id" id="property_sustainability_id" class="form-control" required>
                                        <option value="">اختر العقار</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ old('property_sustainability_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->property->title }} - {{ $property->property->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_sustainability_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_type">نوع التقرير</label>
                                    <select name="report_type" id="report_type" class="form-control" required>
                                        <option value="">اختر نوع التقرير</option>
                                        <option value="comprehensive" {{ old('report_type') == 'comprehensive' ? 'selected' : '' }}>تقرير شامل للاستدامة</option>
                                        <option value="certification" {{ old('report_type') == 'certification' ? 'selected' : '' }}>تقرير شهادة خضراء</option>
                                        <option value="carbon_footprint" {{ old('report_type') == 'carbon_footprint' ? 'selected' : '' }}>تقرير البصمة الكربونية</option>
                                        <option value="energy_efficiency" {{ old('report_type') == 'energy_efficiency' ? 'selected' : '' }}>تقرير كفاءة الطاقة</option>
                                        <option value="water_conservation" {{ old('report_type') == 'water_conservation' ? 'selected' : '' }}>تقرير حفظ المياه</option>
                                        <option value="materials_assessment" {{ old('report_type') == 'materials_assessment' ? 'selected' : '' }}>تقرير تقييم المواد</option>
                                        <option value="climate_impact" {{ old('report_type') == 'climate_impact' ? 'selected' : '' }}>تقرير التأثير المناخي</option>
                                        <option value="performance" {{ old('report_type') == 'performance' ? 'selected' : '' }}>تقرير الأداء</option>
                                        <option value="compliance" {{ old('report_type') == 'compliance' ? 'selected' : '' }}>تقرير الامتثال</option>
                                        <option value="benchmarking" {{ old('report_type') == 'benchmarking' ? 'selected' : '' }}>تقرير المقارنة المعيارية</option>
                                    </select>
                                    @error('report_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">عنوان التقرير</label>
                                    <input type="text" name="title" id="title" class="form-control" 
                                           value="{{ old('title') }}" required>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">وصف التقرير</label>
                                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Period -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">فترة التقرير</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_period_start">تاريخ البداية</label>
                                    <input type="date" name="report_period_start" id="report_period_start" 
                                           class="form-control" value="{{ old('report_period_start') ?? now()->subMonths(3)->format('Y-m-d') }}" required>
                                    @error('report_period_start')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_period_end">تاريخ النهاية</label>
                                    <input type="date" name="report_period_end" id="report_period_end" 
                                           class="form-control" value="{{ old('report_period_end') ?? now()->format('Y-m-d') }}" required>
                                    @error('report_period_end')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Sources -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">مصادر البيانات</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>اختر مصادر البيانات</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="property_data" value="property_data" 
                                                       class="form-check-input" checked>
                                                <label for="property_data" class="form-check-label">بيانات العقار</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="energy_data" value="energy_data" 
                                                       class="form-check-input" checked>
                                                <label for="energy_data" class="form-check-label">بيانات الطاقة</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="water_data" value="water_data" 
                                                       class="form-check-input" checked>
                                                <label for="water_data" class="form-check-label">بيانات المياه</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="materials_data" value="materials_data" 
                                                       class="form-check-input" checked>
                                                <label for="materials_data" class="form-check-label">بيانات المواد</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="carbon_data" value="carbon_data" 
                                                       class="form-check-input" checked>
                                                <label for="carbon_data" class="form-check-label">بيانات الكربون</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="certification_data" value="certification_data" 
                                                       class="form-check-input">
                                                <label for="certification_data" class="form-check-label">بيانات الشهادات</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="climate_data" value="climate_data" 
                                                       class="form-check-input">
                                                <label for="climate_data" class="form-check-label">بيانات المناخ</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="external_data" value="external_data" 
                                                       class="form-check-input">
                                                <label for="external_data" class="form-check-label">بيانات خارجية</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="data_sources[]" id="historical_data" value="historical_data" 
                                                       class="form-check-input">
                                                <label for="historical_data" class="form-check-label">بيانات تاريخية</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="methodology">المنهجية</label>
                                    <textarea name="methodology" id="methodology" class="form-control" rows="3" 
                                              placeholder="صف المنهجية المستخدمة في إعداد التقرير">{{ old('methodology') }}</textarea>
                                    @error('methodology')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Options -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">خيارات التقرير</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المحتوى المطلوب</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_executive_summary" id="include_executive_summary" 
                                               class="form-check-input" checked>
                                        <label for="include_executive_summary" class="form-check-label">ملخص تنفيذي</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_key_findings" id="include_key_findings" 
                                               class="form-check-input" checked>
                                        <label for="include_key_findings" class="form-check-label">النتائج الرئيسية</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_recommendations" id="include_recommendations" 
                                               class="form-check-input" checked>
                                        <label for="include_recommendations" class="form-check-label">التوصيات</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_appendices" id="include_appendices" 
                                               class="form-check-input">
                                        <label for="include_appendices" class="form-check-label">الملاحق</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>خيارات إضافية</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_charts" id="include_charts" 
                                               class="form-check-input" checked>
                                        <label for="include_charts" class="form-check-label">تضمين الرسوم البيانية</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_benchmarking" id="include_benchmarking" 
                                               class="form-check-input">
                                        <label for="include_benchmarking" class="form-check-label">تضمين المقارنات المعيارية</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_trends" id="include_trends" 
                                               class="form-check-input">
                                        <label for="include_trends" class="form-check-label">تضمين اتجاهات الأداء</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_certification_info" id="include_certification_info" 
                                               class="form-check-input">
                                        <label for="include_certification_info" class="form-check-label">تضمين معلومات الشهادات</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">ملاحظات</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="notes">ملاحظات إضافية</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="أضف أي ملاحظات إضافية حول التقرير">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-file-alt"></i> إنشاء التقرير
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="previewReport()">
                                <i class="fas fa-eye"></i> معاينة
                            </button>
                            <a href="{{ route('sustainability.reports.index') }}" class="btn btn-danger btn-lg ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Report Type Info -->
                <div class="card shadow mb-4" id="reportTypeInfo">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">معلومات نوع التقرير</h6>
                    </div>
                    <div class="card-body">
                        <div id="reportTypeDescription">
                            <p class="text-muted">اختر نوع التقرير لعرض المعلومات</p>
                        </div>
                    </div>
                </div>

                <!-- Estimated Time -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">الوقت المقدر للإنشاء</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-clock fa-2x text-gray-300 mb-2"></i>
                            <h5 id="estimatedTime">5-10 دقائق</h5>
                            <p class="text-muted small">يعتمد على حجم البيانات ونوع التقرير</p>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">نصائح</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>اختر فترة مناسبة للحصول على بيانات دقيقة</li>
                            <li>تأكد من اختيار مصادر البيانات الصحيحة</li>
                            <li>استخدم وصفاً واضحاً للتقرير</li>
                            <li>تحقق من البيانات قبل إنشاء التقرير</li>
                            <li>يمكنك إعادة إنشاء التقرير في أي وقت</li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">التقارير الأخيرة</h6>
                    </div>
                    <div class="card-body">
                        @if($recentReports->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentReports as $report)
                                    <div class="list-group-item">
                                        <h6 class="mb-1">{{ $report->title }}</h6>
                                        <small class="text-muted">
                                            {{ $report->report_type_text }} - {{ $report->generated_at->format('Y-m-d') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <div>لا توجد تقارير حديثة</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const reportTypeDescriptions = {
        comprehensive: 'تقرير شامل يغطي جميع جوانب الاستدامة للعقار بما في ذلك الطاقة والمياه والمواد والبصمة الكربونية والتأثير المناخي.',
        certification: 'تقرير متخصص لتقييم أهلية العقار للحصول على شهادات خضراء مثل LEED أو BREEAM.',
        carbon_footprint: 'تقرير يركز على تحليل البصمة الكربونية للعقار وتقديم استراتيجيات لتقليل الانبعاثات.',
        energy_efficiency: 'تقرير متخصص في تحليل كفاءة استهلاك الطاقة وتقديم توصيات لتحسين الأداء.',
        water_conservation: 'تقرير يركز على استهلاك المياه وتقديم حلول للحفاظ على الموارد المائية.',
        materials_assessment: 'تقرير يقيم المواد المستخدمة في العقار من حيث الاستدامة والتأثير البيئي.',
        climate_impact: 'تقرير يحلل تأثير العقار على التغير المناخي ويقدم استراتيجيات التكيف.',
        performance: 'تقرير يقيّم الأداء العام للاستدامة ويقدم مؤشرات الأداء الرئيسية.',
        compliance: 'تقرير يتحقق من الامتثال للمعايير واللوائح البيئية المعمول بها.',
        benchmarking: 'تقرير يقارن أداء العقار بالمعايير الصناعية وأفضل الممارسات.'
    };

    const estimatedTimes = {
        comprehensive: '10-15 دقيقة',
        certification: '8-12 دقيقة',
        carbon_footprint: '5-8 دقائق',
        energy_efficiency: '5-8 دقائق',
        water_conservation: '5-8 دقائق',
        materials_assessment: '6-10 دقائق',
        climate_impact: '8-12 دقيقة',
        performance: '7-10 دقائق',
        compliance: '6-9 دقائق',
        benchmarking: '8-12 دقيقة'
    };

    document.getElementById('report_type').addEventListener('change', function() {
        const reportType = this.value;
        const description = reportTypeDescriptions[reportType] || 'اختر نوع التقرير لعرض المعلومات';
        const estimatedTime = estimatedTimes[reportType] || '5-10 دقائق';
        
        document.getElementById('reportTypeDescription').innerHTML = `<p>${description}</p>`;
        document.getElementById('estimatedTime').textContent = estimatedTime;
    });

    function previewReport() {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        
        // Basic validation
        if (!formData.get('property_sustainability_id') || !formData.get('report_type')) {
            alert('يرجى اختيار العقار ونوع التقرير');
            return;
        }
        
        // Open preview in new window
        const params = new URLSearchParams(formData);
        window.open('{{ route("sustainability.reports.preview") }}?' + params.toString(), '_blank');
    }

    // Auto-update title based on report type and property
    function updateTitle() {
        const propertySelect = document.getElementById('property_sustainability_id');
        const reportTypeSelect = document.getElementById('report_type');
        const titleInput = document.getElementById('title');
        
        if (propertySelect.value && reportTypeSelect.value && !titleInput.value) {
            const propertyText = propertySelect.options[propertySelect.selectedIndex].text;
            const reportTypeText = reportTypeSelect.options[reportTypeSelect.selectedIndex].text;
            titleInput.value = `${reportTypeText} - ${propertyText}`;
        }
    }

    document.getElementById('property_sustainability_id').addEventListener('change', updateTitle);
    document.getElementById('report_type').addEventListener('change', updateTitle);

    // Date validation
    document.getElementById('report_period_start').addEventListener('change', validateDateRange);
    document.getElementById('report_period_end').addEventListener('change', validateDateRange);

    function validateDateRange() {
        const startDate = document.getElementById('report_period_start').value;
        const endDate = document.getElementById('report_period_end').value;
        
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            alert('تاريخ البداية يجب أن يكون قبل أو يساوي تاريخ النهاية');
            document.getElementById('report_period_end').value = startDate;
        }
    }

    // Show loading state on submit
    document.querySelector('form').addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء التقرير...';
        submitButton.disabled = true;
    });
</script>
@endpush
