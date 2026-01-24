<?php

namespace App\Http\Requests\Neighborhood;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNeighborhoodStatisticRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'neighborhood_id' => ['required', 'exists:neighborhoods,id'],
            'statistic_type' => ['required', 'string', 'in:population,property,business,amenity,safety,education,transportation,healthcare,recreation,economic,demographic,infrastructure'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'period' => ['required', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
            'data_source' => ['nullable', 'string', 'max:255'],
            'collection_method' => ['nullable', 'string', 'max:100'],
            'collection_date' => ['nullable', 'date', 'before_or_equal:today'],
            'data_points' => ['nullable', 'array'],
            'data_points.*.value' => ['nullable', 'numeric'],
            'data_points.*.date' => ['nullable', 'date'],
            'data_points.*.label' => ['nullable', 'string', 'max:255'],
            'aggregated_data' => ['nullable', 'array'],
            'aggregated_data.total' => ['nullable', 'numeric'],
            'aggregated_data.average' => ['nullable', 'numeric'],
            'aggregated_data.minimum' => ['nullable', 'numeric'],
            'aggregated_data.maximum' => ['nullable', 'numeric'],
            'aggregated_data.median' => ['nullable', 'numeric'],
            'aggregated_data.count' => ['nullable', 'integer', 'min:0'],
            'trend_analysis' => ['nullable', 'array'],
            'trend_analysis.trend' => ['nullable', 'string', 'in:increasing,decreasing,stable,volatile'],
            'trend_analysis.percentage_change' => ['nullable', 'numeric'],
            'trend_analysis.confidence_level' => ['nullable', 'numeric', 'between:0,100'],
            'trend_analysis.analysis_period' => ['nullable', 'string', 'max:50'],
            'comparative_data' => ['nullable', 'array'],
            'comparative_data.previous_period' => ['nullable', 'numeric'],
            'comparative_data.percentage_change' => ['nullable', 'numeric'],
            'comparative_data.benchmark' => ['nullable', 'numeric'],
            'comparative_data.period' => ['nullable', 'string', 'max:50'],
            'forecast_data' => ['nullable', 'array'],
            'forecast_data.next_period' => ['nullable', 'numeric'],
            'forecast_data.confidence_level' => ['nullable', 'numeric', 'between:0,100'],
            'forecast_data.method' => ['nullable', 'string', 'max:50'],
            'forecast_data.period' => ['nullable', 'string', 'max:50'],
            'visualization_data' => ['nullable', 'array'],
            'visualization_data.chart_type' => ['nullable', 'string', 'in:line,bar,pie,area,scatter,radar'],
            'visualization_data.color_scheme' => ['nullable', 'array'],
            'visualization_data.data_format' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,xlsx,csv,doc,docx', 'max:10240'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'neighborhood_id.required' => 'الحي مطلوب',
            'neighborhood_id.exists' => 'الحي المحدد غير موجود',
            'statistic_type.required' => 'نوع الإحصائية مطلوب',
            'statistic_type.in' => 'نوع الإحصائية غير صالح',
            'title.required' => 'عنوان الإحصائية مطلوب',
            'title.max' => 'يجب ألا يتجاوز عنوان الإحصائية 255 حرفًا',
            'description.max' => 'يجب ألا يتجاوز وصف الإحصائية 2000 حرف',
            'period.required' => 'الفترة مطلوبة',
            'period.in' => 'الفترة غير صالحة',
            'data_source.max' => 'يجب ألا يتجاوز مصدر البيانات 255 حرفًا',
            'collection_method.max' => 'يجب ألا يتجاوز طريقة الجمع 100 حرف',
            'collection_date.date' => 'تاريخ الجمع يجب أن يكون تاريخًا صالحًا',
            'collection_date.before_or_equal' => 'تاريخ الجمع يجب أن يكون اليوم أو في الماضي',
            'data_points.*.value.numeric' => 'قيمة نقطة البيانات يجب أن تكون رقمًا',
            'data_points.*.date.date' => 'تاريخ نقطة البيانات يجب أن يكون تاريخًا صالحًا',
            'data_points.*.label.max' => 'يجب ألا يتجاوز تسمية نقطة البيانات 255 حرفًا',
            'aggregated_data.total.numeric' => 'الإجمالي يجب أن يكون رقمًا',
            'aggregated_data.average.numeric' => 'المتوسط يجب أن يكون رقمًا',
            'aggregated_data.minimum.numeric' => 'الحد الأدنى يجب أن يكون رقمًا',
            'aggregated_data.maximum.numeric' => 'الحد الأقصى يجب أن يكون رقمًا',
            'aggregated_data.median.numeric' => 'الوسيط يجب أن يكون رقمًا',
            'aggregated_data.count.integer' => 'العدد يجب أن يكون رقمًا صحيحًا',
            'aggregated_data.count.min' => 'العدد يجب أن يكون 0 أو أكثر',
            'trend_analysis.trend.in' => 'الاتجاه غير صالح',
            'trend_analysis.percentage_change.numeric' => 'نسبة التغيير يجب أن تكون رقمًا',
            'trend_analysis.confidence_level.between' => 'مستوى الثقة يجب أن يكون بين 0 و 100',
            'trend_analysis.analysis_period.max' => 'يجب ألا يتجاوز فترة التحليل 50 حرفًا',
            'comparative_data.previous_period.numeric' => 'الفترة السابقة يجب أن تكون رقمًا',
            'comparative_data.percentage_change.numeric' => 'نسبة التغيير يجب أن تكون رقمًا',
            'comparative_data.benchmark.numeric' => 'المعيار يجب أن يكون رقمًا',
            'comparative_data.period.max' => 'يجب ألا يتجاوز فترة المقارنة 50 حرفًا',
            'forecast_data.next_period.numeric' => 'الفترة التالية يجب أن تكون رقمًا',
            'forecast_data.confidence_level.between' => 'مستوى الثقة يجب أن يكون بين 0 و 100',
            'forecast_data.method.max' => 'يجب ألا يتجاوز طريقة التنبؤ 50 حرفًا',
            'forecast_data.period.max' => 'يجب ألا يتجاوز فترة التنبؤ 50 حرفًا',
            'visualization_data.chart_type.in' => 'نوع المخطط غير صالح',
            'visualization_data.data_format.max' => 'يجب ألا يتجاوز تنسيق البيانات 50 حرفًا',
            'attachments.*.file' => 'يجب أن يكون الملف ملفًا صالحًا',
            'attachments.*.mimes' => 'يجب أن يكون الملف بصيغة pdf, xlsx, csv, doc, أو docx',
            'attachments.*.max' => 'يجب ألا يتجاوز حجم الملف 10 ميجابايت',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'neighborhood_id' => 'الحي',
            'statistic_type' => 'نوع الإحصائية',
            'title' => 'عنوان الإحصائية',
            'description' => 'وصف الإحصائية',
            'period' => 'الفترة',
            'data_source' => 'مصدر البيانات',
            'collection_method' => 'طريقة الجمع',
            'collection_date' => 'تاريخ الجمع',
            'data_points' => 'نقاطف البيانات',
            'data_points.*.value' => 'قيمة نقطة البيانات',
            'data_points.*.date' => 'تاريخ نقطة البيانات',
            'data_points.*.label' => 'تسمية نقطة البيانات',
            'aggregated_data' => 'البيانات المجمعة',
            'aggregated_data.total' => 'الإجمالي',
            'aggregated_data.average' => 'المتوسط',
            'aggregated_data.minimum' => 'الحد الأدنى',
            'aggregated_data.maximum' => 'الحد الأقصى',
            'aggregated_data.median' => 'الوسيط',
            'aggregated_data.count' => 'العدد',
            'trend_analysis' => 'تحليل الاتجاه',
            'trend_analysis.trend' => 'الاتجاه',
            'trend_analysis.percentage_change' => 'نسبة التغيير',
            'trend_analysis.confidence_level' => 'مستوى الثقة',
            'trend_analysis.analysis_period' => 'فترة التحليل',
            'comparative_data' => 'البيانات المقارنة',
            'comparative_data.previous_period' => 'الفترة السابقة',
            'comparative_data.percentage_change' => 'نسبة التغيير',
            'comparative_data.benchmark' => 'المعيار',
            'comparative_data.period' => 'فترة المقارنة',
            'forecast_data' => 'بيانات التنبؤ',
            'forecast_data.next_period' => 'الفترة التالية',
            'forecast_data.confidence_level' => 'مستوى الثقة',
            'forecast_data.method' => 'طريقة التنبؤ',
            'forecast_data.period' => 'فترة التنبؤ',
            'visualization_data' => 'بيانات التصور',
            'visualization_data.chart_type' => 'نوع المخطط',
            'visualization_data.color_scheme' => 'مخطط الألوان',
            'visualization_data.data_format' => 'تنسيق البيانات',
            'metadata' => 'البيانات الوصفية',
            'attachments' => 'المرفقات',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate data points
            if ($this->has('data_points') && is_array($this->data_points)) {
                if (count($this->data_points) > 1000) {
                    $validator->errors()->add('data_points', 'يجب ألا يتجاوز عدد نقاطف البيانات 1000');
                }
                
                foreach ($this->data_points as $index => $point) {
                    if (!is_array($point)) {
                        $validator->errors()->add("data_points.{$index}", 'نقطة البيانات يجب أن تكون مصفوفة');
                        continue;
                    }
                    
                    if (isset($point['date']) && isset($point['value'])) {
                        $date = $point['date'];
                        $value = $point['value'];
                        
                        if (is_string($date)) {
                            try {
                                $carbonDate = \Carbon\Carbon::parse($date);
                                
                                // Validate date is not in future for historical data
                                if ($carbonDate->isFuture()) {
                                    $validator->errors()->add("data_points.{$index}.date", 'تاريخ نقطة البيانات يجب أن يكون في الماضي أو الحاضر');
                                }
                            } catch (\Exception $e) {
                                $validator->errors()->add("data_points.{$index}.date", 'تاريخ نقطة البيانات غير صالح');
                            }
                        }
                        
                        if (!is_numeric($value)) {
                            $validator->errors()->add("data_points.{$index}.value", 'قيمة نقطة البيانات يجب أن تكون رقمًا');
                        }
                    }
                }
            }
            
            // Validate aggregated data consistency
            if ($this->has('aggregated_data') && is_array($this->aggregated_data)) {
                $aggregated = $this->aggregated_data;
                
                if (isset($aggregated['minimum']) && isset($aggregated['maximum'])) {
                    if ($aggregated['minimum'] > $aggregated['maximum']) {
                        $validator->errors()->add('aggregated_data.minimum', 'الحد الأدنى يجب أن يكون أقل من الحد الأقصى');
                    }
                }
                
                if (isset($aggregated['minimum']) && isset($aggregated['average'])) {
                    if ($aggregated['average'] < $aggregated['minimum']) {
                        $validator->errors()->add('aggregated_data.average', 'المتوسط يجب أن يكون أكبر من الحد الأدنى');
                    }
                }
                
                if (isset($aggregated['maximum']) && isset($aggregated['average'])) {
                    if ($aggregated['average'] > $aggregated['maximum']) {
                        $validator->errors()->add('aggregated_data.average', 'المتوسط يجب أن يكون أقل من الحد الأقصى');
                    }
                }
            }
            
            // Validate trend analysis
            if ($this->has('trend_analysis') && is_array($this->trend_analysis)) {
                $trend = $this->trend_analysis;
                
                if (isset($trend['percentage_change']) && isset($trend['trend'])) {
                    $change = $trend['percentage_change'];
                    $trendType = $trend['trend'];
                    
                    if ($trendType === 'increasing' && $change <= 0) {
                        $validator->errors()->add('trend_analysis.percentage_change', 'نسبة التغيير يجب أن تكون موجبة للاتجاه المتزايد');
                    }
                    
                    if ($trendType === 'decreasing' && $change >= 0) {
                        $validator->errors()->add('trend_analysis.percentage_change', 'نسبة التغيير يجب أن تكون سالبة للاتجاه المتناقص');
                    }
                }
            }
            
            // Validate forecast data
            if ($this->has('forecast_data') && is_array($this->forecast_data)) {
                $forecast = $this->forecast_data;
                
                if (isset($forecast['confidence_level']) && isset($forecast['next_period'])) {
                    $confidence = $forecast['confidence_level'];
                    $nextPeriod = $forecast['next_period'];
                    
                    if ($confidence < 50 && abs($nextPeriod) > 1000) {
                        $validator->errors()->add('forecast_data.confidence_level', 'مستوى الثقة المنخفض يتطلب قيم تنبؤ أصغر');
                    }
                }
            }
            
            // Validate visualization data
            if ($this->has('visualization_data') && is_array($this->visualization_data)) {
                $visualization = $this->visualization_data;
                
                if (isset($visualization['color_scheme']) && is_array($visualization['color_scheme'])) {
                    if (count($visualization['color_scheme']) > 10) {
                        $validator->errors()->add('visualization_data.color_scheme', 'يجب ألا يتجاوز مخطط الألوان 10 ألوان');
                    }
                }
            }
            
            // Validate attachments
            if ($this->has('attachments') && is_array($this->attachments)) {
                if (count($this->attachments) > 10) {
                    $validator->errors()->add('attachments', 'يجب ألا يتجاوز عدد المرفقات 10');
                }
            }
            
            // Validate period consistency with collection date
            if ($this->has('period') && $this->has('collection_date')) {
                $period = $this->period;
                $collectionDate = $this->collection_date;
                
                if ($collectionDate && is_string($collectionDate)) {
                    $carbonDate = \Carbon\Carbon::parse($collectionDate);
                    $now = \Carbon\Carbon::now();
                    
                    switch ($period) {
                        case 'daily':
                            if ($carbonDate->diffInDays($now) > 7) {
                                $validator->errors()->add('collection_date', 'تاريخ الجمع للبيانات اليومية يجب أن يكون خلال 7 أيام');
                            }
                            break;
                        case 'weekly':
                            if ($carbonDate->diffInWeeks($now) > 12) {
                                $validator->errors()->add('collection_date', 'تاريخ الجمع للبيانات الأسبوعية يجب أن يكون خلال 12 أسبوعًا');
                            }
                            break;
                        case 'monthly':
                            if ($carbonDate->diffInMonths($now) > 24) {
                                $validator->errors()->add('collection_date', 'تاريخ الجمع للبيانات الشهرية يجب أن يكون خلال 24 شهرًا');
                            }
                            break;
                        case 'quarterly':
                            if ($carbonDate->diffInQuarters($now) > 16) {
                                $validator->errors()->add('collection_date', 'تاريخ الجمع للبيانات الربع سنوية يجب أن يكون خلال 16 ربعًا');
                            }
                            break;
                        case 'yearly':
                            if ($carbonDate->diffInYears($now) > 10) {
                                $validator->errors()->add('collection_date', 'تاريخ الجمع للبيانات السنوية يجب أن يكون خلال 10 سنوات');
                            }
                            break;
                    }
                }
            }
        });
    }
}
