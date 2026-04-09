@extends('admin.layouts.admin')

@section('title', $survey->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $survey->title }}</h1>
        <div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-warning shadow-sm">
                    <i class="fas fa-edit fa-sm text-white-50"></i> تعديل
                </a>
            @endif
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-right fa-sm text-white-50"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Survey Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل الاستبيان</h6>
                </div>
                <div class="card-body">
                    @if($survey->description)
                        <div class="mb-4">
                            <h5 class="font-weight-bold">الوصف:</h5>
                            <p>{{ $survey->description }}</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>تاريخ البدء:</strong> {{ $survey->starts_at->format('Y-m-d') }}
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الانتهاء:</strong> 
                            {{ $survey->expires_at ? $survey->expires_at->format('Y-m-d') : 'مفتوح' }}
                        </div>
                    </div>

                    <hr>

                    @if($canParticipate && !$hasResponded)
                        <form action="{{ route('surveys.submit', $survey->id) }}" method="POST">
                            @csrf
                            
                            @foreach($survey->questions as $index => $question)
                                <div class="question-item mb-4">
                                    <h5 class="font-weight-bold">
                                        {{ $index + 1 }}. {{ $question->question_text }}
                                        @if($question->is_required) <span class="text-danger">*</span> @endif
                                    </h5>

                                    <div class="mt-2">
                                        @if($question->question_type === 'text')
                                            <input type="text" class="form-control" name="responses[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}>
                                        
                                        @elseif($question->question_type === 'textarea')
                                            <textarea class="form-control" name="responses[{{ $question->id }}]" rows="3" {{ $question->is_required ? 'required' : '' }}></textarea>
                                        
                                        @elseif($question->question_type === 'multiple_choice')
                                            @foreach($question->options as $option)
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="q{{ $question->id }}_opt{{ $loop->index }}" name="responses[{{ $question->id }}]" value="{{ $option }}" class="custom-control-input" {{ $question->is_required ? 'required' : '' }}>
                                                    <label class="custom-control-label" for="q{{ $question->id }}_opt{{ $loop->index }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        
                                        @elseif($question->question_type === 'checkbox')
                                            @foreach($question->options as $option)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="q{{ $question->id }}_opt{{ $loop->index }}" name="responses[{{ $question->id }}][]" value="{{ $option }}" class="custom-control-input">
                                                    <label class="custom-control-label" for="q{{ $question->id }}_opt{{ $loop->index }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        
                                        @elseif($question->question_type === 'dropdown')
                                            <select class="form-control" name="responses[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}>
                                                <option value="">اختر إجابة...</option>
                                                @foreach($question->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane mr-2"></i> إرسال الإجابات
                                </button>
                            </div>
                        </form>
                    @elseif($hasResponded)
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x mb-3"></i>
                            <h4>شكراً لك!</h4>
                            <p>لقد قمت بالإجابة على هذا الاستبيان مسبقاً.</p>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            هذا الاستبيان غير متاح حالياً أو ليس لديك صلاحية الوصول إليه.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistics Sidebar -->
            @if($showStatistics && $statistics)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">إحصائيات الاستبيان</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <div class="h1 font-weight-bold text-primary">{{ $statistics['total_responses'] }}</div>
                            <div class="text-muted">إجمالي الردود</div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <strong>الحالة:</strong>
                            <span class="badge badge-{{ $survey->status == 'published' ? 'success' : 'secondary' }}">
                                {{ $survey->status == 'published' ? 'منشور' : 'مسودة' }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>الجمهور المستهدف:</strong>
                            <span class="badge badge-info">{{ $survey->target_audience }}</span>
                        </div>

                        @if($survey->is_anonymous)
                            <div class="mb-3">
                                <i class="fas fa-user-secret text-muted mr-2"></i> استبيان مجهول الهوية
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection