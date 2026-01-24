<?php

namespace App\Http\Controllers;

use App\Models\CustomReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomReportController extends Controller
{
    public function index()
    {
        $reports = CustomReport::with(['user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('reports.custom.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.custom.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'data_sources' => 'required|array',
            'data_sources.*' => 'required|in:properties,transactions,users,reviews,agents,companies',
            'filters' => 'nullable|array',
            'columns' => 'required|array',
            'columns.*' => 'required|string',
            'group_by' => 'nullable|array',
            'group_by.*' => 'required|string',
            'aggregations' => 'nullable|array',
            'aggregations.*.column' => 'required|string',
            'aggregations.*.function' => 'required|in:sum,avg,count,min,max',
            'sort_by' => 'nullable|array',
            'sort_by.column' => 'required_with:sort_by|string',
            'sort_by.direction' => 'required_with:sort_by|in:asc,desc',
            'limit' => 'nullable|integer|min:1|max:1000',
            'format' => 'required|in:pdf,excel,csv,html'
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
            'template_id' => 4, // Custom report template
            'parameters' => [
                'data_sources' => $validated['data_sources'],
                'filters' => $validated['filters'] ?? [],
                'columns' => $validated['columns'],
                'group_by' => $validated['group_by'] ?? [],
                'aggregations' => $validated['aggregations'] ?? [],
                'sort_by' => $validated['sort_by'] ?? [],
                'limit' => $validated['limit'] ?? 100
            ],
            'format' => $validated['format'],
            'status' => 'pending'
        ]);

        return redirect()->route('reports.custom.show', $report->id)
            ->with('success', 'تم إنشاء التقرير المخصص بنجاح');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        
        if ($report->template_id !== 4) {
            return back()->with('error', 'هذا ليس تقرير مخصص');
        }
        
        $customData = $this->getCustomReportData($report);
        
        return view('reports.custom.show', compact('report', 'customData'));
    }

    public function builder()
    {
        $dataSources = $this->getAvailableDataSources();
        $columns = $this->getAvailableColumns();
        
        return view('reports.custom.builder', compact('dataSources', 'columns'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'data_sources' => 'required|array',
            'data_sources.*' => 'required|in:properties,transactions,users,reviews,agents,companies',
            'filters' => 'nullable|array',
            'columns' => 'required|array',
            'columns.*' => 'required|string',
            'group_by' => 'nullable|array',
            'aggregations' => 'nullable|array',
            'sort_by' => 'nullable|array',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        try {
            $data = $this->executeCustomQuery($validated);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function templates()
    {
        $templates = $this->getCustomReportTemplates();
        
        return view('reports.custom.templates', compact('templates'));
    }

    public function saveTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'configuration' => 'required|array',
            'is_public' => 'nullable|boolean'
        ]);

        $template = Auth::user()->customReportTemplates()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'configuration' => $validated['configuration'],
            'is_public' => $validated['is_public'] ?? false
        ]);

        return back()->with('success', 'تم حفظ القالب بنجاح');
    }

    public function loadTemplate($templateId)
    {
        $template = Auth::user()->customReportTemplates()
            ->findOrFail($templateId);
            
        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    private function getCustomReportData(Report $report)
    {
        $parameters = $report->parameters ?? [];
        
        $data = $this->executeCustomQuery($parameters);
        
        return [
            'data' => $data,
            'summary' => $this->generateDataSummary($data, $parameters),
            'charts' => $this->generateChartData($data, $parameters),
            'metadata' => [
                'total_records' => count($data),
                'generated_at' => now(),
                'parameters' => $parameters
            ]
        ];
    }

    private function executeCustomQuery($parameters)
    {
        $dataSources = $parameters['data_sources'] ?? [];
        $filters = $parameters['filters'] ?? [];
        $columns = $parameters['columns'] ?? [];
        $groupBy = $parameters['group_by'] ?? [];
        $aggregations = $parameters['aggregations'] ?? [];
        $sortBy = $parameters['sort_by'] ?? [];
        $limit = $parameters['limit'] ?? 100;

        $query = $this->buildQuery($dataSources, $columns);
        
        $this->applyFilters($query, $filters);
        $this->applyGroupBy($query, $groupBy, $aggregations);
        $this->applySorting($query, $sortBy);
        $this->applyLimit($query, $limit);
        
        return $query->get();
    }

    private function buildQuery($dataSources, $columns)
    {
        $query = null;
        
        foreach ($dataSources as $source) {
            switch ($source) {
                case 'properties':
                    $tableQuery = DB::table('properties')
                        ->select($this->mapColumns($columns, 'properties'));
                    break;
                    
                case 'transactions':
                    $tableQuery = DB::table('transactions')
                        ->select($this->mapColumns($columns, 'transactions'));
                    break;
                    
                case 'users':
                    $tableQuery = DB::table('users')
                        ->select($this->mapColumns($columns, 'users'));
                    break;
                    
                case 'reviews':
                    $tableQuery = DB::table('reviews')
                        ->select($this->mapColumns($columns, 'reviews'));
                    break;
                    
                case 'agents':
                    $tableQuery = DB::table('agents')
                        ->select($this->mapColumns($columns, 'agents'));
                    break;
                    
                case 'companies':
                    $tableQuery = DB::table('companies')
                        ->select($this->mapColumns($columns, 'companies'));
                    break;
                    
                default:
                    continue 2;
            }
            
            if ($query === null) {
                $query = $tableQuery;
            } else {
                // Handle joins between tables
                $query = $this->joinTables($query, $tableQuery, $source);
            }
        }
        
        return $query;
    }

    private function mapColumns($columns, $table)
    {
        $mappedColumns = [];
        
        foreach ($columns as $column) {
            if (strpos($column, '.') === false) {
                $mappedColumns[] = "{$table}.{$column}";
            } else {
                $mappedColumns[] = $column;
            }
        }
        
        return $mappedColumns;
    }

    private function joinTables($query, $tableQuery, $source)
    {
        // Implementation for joining tables based on relationships
        // This is a simplified version - in practice, you'd need more sophisticated logic
        
        switch ($source) {
            case 'transactions':
                return $query->join('transactions', 'properties.id', '=', 'transactions.property_id');
                
            case 'reviews':
                return $query->leftJoin('reviews', 'properties.id', '=', 'reviews.property_id');
                
            case 'users':
                return $query->join('users', 'properties.user_id', '=', 'users.id');
                
            default:
                return $query;
        }
    }

    private function applyFilters($query, $filters)
    {
        foreach ($filters as $filter) {
            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $filter['value'];
            
            switch ($operator) {
                case 'equals':
                    $query->where($column, $value);
                    break;
                    
                case 'contains':
                    $query->where($column, 'LIKE', "%{$value}%");
                    break;
                    
                case 'greater_than':
                    $query->where($column, '>', $value);
                    break;
                    
                case 'less_than':
                    $query->where($column, '<', $value);
                    break;
                    
                case 'between':
                    $query->whereBetween($column, [$value['start'], $value['end']]);
                    break;
                    
                case 'in':
                    $query->whereIn($column, $value);
                    break;
                    
                case 'not_null':
                    $query->whereNotNull($column);
                    break;
            }
        }
    }

    private function applyGroupBy($query, $groupBy, $aggregations)
    {
        if (!empty($groupBy)) {
            $query->groupBy($groupBy);
            
            // Apply aggregations
            foreach ($aggregations as $aggregation) {
                $column = $aggregation['column'];
                $function = $aggregation['function'];
                
                switch ($function) {
                    case 'sum':
                        $query->selectRaw("SUM({$column}) as {$column}_sum");
                        break;
                        
                    case 'avg':
                        $query->selectRaw("AVG({$column}) as {$column}_avg");
                        break;
                        
                    case 'count':
                        $query->selectRaw("COUNT({$column}) as {$column}_count");
                        break;
                        
                    case 'min':
                        $query->selectRaw("MIN({$column}) as {$column}_min");
                        break;
                        
                    case 'max':
                        $query->selectRaw("MAX({$column}) as {$column}_max");
                        break;
                }
            }
        }
    }

    private function applySorting($query, $sortBy)
    {
        if (!empty($sortBy)) {
            $column = $sortBy['column'];
            $direction = $sortBy['direction'];
            
            $query->orderBy($column, $direction);
        }
    }

    private function applyLimit($query, $limit)
    {
        if ($limit > 0) {
            $query->limit($limit);
        }
    }

    private function generateDataSummary($data, $parameters)
    {
        $summary = [
            'total_records' => count($data),
            'numeric_summary' => []
        ];
        
        // Generate summary for numeric columns
        $columns = $parameters['columns'] ?? [];
        foreach ($columns as $column) {
            $values = $data->pluck($column)->filter(function($value) {
                return is_numeric($value);
            });
            
            if ($values->isNotEmpty()) {
                $summary['numeric_summary'][$column] = [
                    'min' => $values->min(),
                    'max' => $values->max(),
                    'avg' => $values->avg(),
                    'sum' => $values->sum(),
                    'count' => $values->count()
                ];
            }
        }
        
        return $summary;
    }

    private function generateChartData($data, $parameters)
    {
        $charts = [];
        $groupBy = $parameters['group_by'] ?? [];
        
        if (!empty($groupBy)) {
            // Generate chart data for grouped data
            foreach ($groupBy as $group) {
                $groupedData = $data->groupBy($group);
                
                $charts[$group] = [
                    'labels' => $groupedData->keys(),
                    'datasets' => [
                        [
                            'label' => 'Count',
                            'data' => $groupedData->map->count()->values()
                        ]
                    ]
                ];
            }
        }
        
        return $charts;
    }

    private function getAvailableDataSources()
    {
        return [
            'properties' => 'العقارات',
            'transactions' => 'المعاملات',
            'users' => 'المستخدمون',
            'reviews' => 'التقييمات',
            'agents' => 'الوكلاء',
            'companies' => 'الشركات'
        ];
    }

    private function getAvailableColumns()
    {
        return [
            'properties' => [
                'id' => 'المعرف',
                'title' => 'العنوان',
                'description' => 'الوصف',
                'price' => 'السعر',
                'location' => 'الموقع',
                'type' => 'النوع',
                'status' => 'الحالة',
                'views_count' => 'عدد المشاهدات',
                'inquiries_count' => 'عدد الاستفسارات',
                'created_at' => 'تاريخ الإنشاء',
                'updated_at' => 'تاريخ التحديث'
            ],
            'transactions' => [
                'id' => 'المعرف',
                'property_id' => 'معرف العقار',
                'amount' => 'المبلغ',
                'status' => 'الحالة',
                'type' => 'النوع',
                'created_at' => 'تاريخ الإنشاء'
            ],
            'users' => [
                'id' => 'المعرف',
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'role' => 'الدور',
                'created_at' => 'تاريخ الإنشاء'
            ],
            'reviews' => [
                'id' => 'المعرف',
                'rating' => 'التقييم',
                'comment' => 'التعليق',
                'status' => 'الحالة',
                'created_at' => 'تاريخ الإنشاء'
            ],
            'agents' => [
                'id' => 'المعرف',
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'phone' => 'الهاتف',
                'license' => 'الرخصة',
                'created_at' => 'تاريخ الإنشاء'
            ],
            'companies' => [
                'id' => 'المعرف',
                'name' => 'الاسم',
                'description' => 'الوصف',
                'website' => 'الموقع الإلكتروني',
                'created_at' => 'تاريخ الإنشاء'
            ]
        ];
    }

    private function getCustomReportTemplates()
    {
        return Auth::user()->customReportTemplates()
            ->with('user')
            ->latest()
            ->get();
    }
}
