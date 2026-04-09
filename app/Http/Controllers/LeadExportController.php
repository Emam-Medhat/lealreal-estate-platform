<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadExportController extends Controller
{
    public function index()
    {
        $exports = cache()->get('lead_exports', []);
        
        return view('lead-export.index', compact('exports'));
    }
    
    public function create()
    {
        $sources = LeadSource::all();
        $statuses = LeadStatus::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-export.create', compact('sources', 'statuses', 'users'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'format' => 'required|in:csv,xlsx,pdf',
            'filters' => 'nullable|array',
            'fields' => 'required|array',
            'fields.*' => 'string',
            'include_activities' => 'boolean',
            'include_notes' => 'boolean',
            'date_range' => 'nullable|string',
        ]);
        
        $exportId = time();
        
        // Store export info
        $export = [
            'id' => $exportId,
            'name' => $request->name,
            'format' => $request->format,
            'filters' => $request->filters ?? [],
            'fields' => $request->fields,
            'include_activities' => $request->boolean('include_activities'),
            'include_notes' => $request->boolean('include_notes'),
            'date_range' => $request->date_range,
            'status' => 'processing',
            'total_records' => 0,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ];
        
        $exports = cache()->get('lead_exports', []);
        $exports[] = $export;
        cache()->put('lead_exports', $exports, now()->addDays(30));
        
        // Process export in background
        $this->processExport($exportId, $request->all());
        
        return redirect()->route('lead-export.show', $exportId)
            ->with('success', 'تم بدء عملية تصدير العملاء المحتملين');
    }
    
    public function show($exportId)
    {
        $exports = cache()->get('lead_exports', []);
        $export = collect($exports)->firstWhere('id', $exportId);
        
        if (!$export) {
            abort(404);
        }
        
        return view('lead-export.show', compact('export'));
    }
    
    public function download($exportId)
    {
        $exports = cache()->get('lead_exports', []);
        $export = collect($exports)->firstWhere('id', $exportId);
        
        if (!$export || $export['status'] !== 'completed') {
            abort(404);
        }
        
        $filename = $export['filename'] ?? 'lead_export_' . $exportId . '.' . $export['format'];
        $filepath = storage_path('app/exports/' . $filename);
        
        if (!file_exists($filepath)) {
            abort(404);
        }
        
        return response()->download($filepath)->deleteFileAfterSend(true);
    }
    
    public function quickExport(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'exists:leads,id',
        ]);
        
        $leads = Lead::query();
        
        if ($request->has('lead_ids')) {
            $leads->whereIn('id', $request->lead_ids);
        }
        
        $leads = $leads->with(['source', 'status', 'assignedUser'])->get();
        
        $data = [];
        foreach ($leads as $lead) {
            $data[] = [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'company' => $lead->company,
                'position' => $lead->position,
                'source' => $lead->source->name ?? '',
                'status' => $lead->status->name ?? '',
                'assigned_to' => $lead->assignedUser->name ?? '',
                'estimated_value' => $lead->estimated_value,
                'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
                'last_contact_at' => $lead->last_contact_at?->format('Y-m-d H:i:s'),
                'converted_at' => $lead->converted_at?->format('Y-m-d H:i:s'),
            ];
        }
        
        $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.' . $request->format;
        
        if ($request->format === 'csv') {
            return $this->exportCSV($data, $filename);
        } else {
            return $this->exportExcel($data, $filename);
        }
    }
    
    public function destroy($exportId)
    {
        $exports = cache()->get('lead_exports', []);
        $exportIndex = collect($exports)->search(function($export) use ($exportId) {
            return $export['id'] == $exportId;
        });
        
        if ($exportIndex === false) {
            abort(404);
        }
        
        // Delete file if exists
        $export = $exports[$exportIndex];
        if (isset($export['filename'])) {
            $filepath = storage_path('app/exports/' . $export['filename']);
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        
        unset($exports[$exportIndex]);
        $exports = array_values($exports);
        cache()->put('lead_exports', $exports, now()->addDays(30));
        
        return redirect()->route('lead-export.index')
            ->with('success', 'تم حذف التصدير بنجاح');
    }
    
    private function processExport($exportId, $options)
    {
        try {
            $leads = $this->getFilteredLeads($options['filters'] ?? []);
            $totalRecords = $leads->count();
            
            $data = [];
            foreach ($leads as $lead) {
                $row = $this->formatLeadRow($lead, $options['fields'], $options);
                
                if ($options['include_activities']) {
                    $row['activities'] = $this->formatActivities($lead->activities);
                }
                
                if ($options['include_notes']) {
                    $row['notes'] = $this->formatNotes($lead->notes);
                }
                
                $data[] = $row;
            }
            
            $filename = 'lead_export_' . $exportId . '.' . $options['format'];
            $filepath = storage_path('app/exports/' . $filename);
            
            // Ensure directory exists
            $directory = dirname($filepath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Generate file based on format
            switch ($options['format']) {
                case 'csv':
                    $this->generateCSVFile($data, $filepath, $options['fields']);
                    break;
                case 'xlsx':
                    $this->generateExcelFile($data, $filepath, $options['fields']);
                    break;
                case 'pdf':
                    $this->generatePDFFile($data, $filepath, $options['fields']);
                    break;
            }
            
            // Update export status
            $this->updateExportStatus($exportId, [
                'status' => 'completed',
                'total_records' => $totalRecords,
                'filename' => $filename,
                'completed_at' => now(),
            ]);
            
        } catch (\Exception $e) {
            $this->updateExportStatus($exportId, [
                'status' => 'failed',
                'errors' => [$e->getMessage()],
                'completed_at' => now(),
            ]);
        }
    }
    
    private function getFilteredLeads($filters)
    {
        $leads = Lead::with(['source', 'status', 'assignedUser', 'activities', 'notes']);
        
        // Apply filters
        if (isset($filters['source_id'])) {
            $leads->where('source_id', $filters['source_id']);
        }
        
        if (isset($filters['status_id'])) {
            $leads->where('status_id', $filters['status_id']);
        }
        
        if (isset($filters['assigned_to'])) {
            $leads->where('assigned_to', $filters['assigned_to']);
        }
        
        if (isset($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) === 2) {
                $leads->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }
        
        if (isset($filters['converted'])) {
            if ($filters['converted'] === 'yes') {
                $leads->whereNotNull('converted_at');
            } else {
                $leads->whereNull('converted_at');
            }
        }
        
        return $leads->get();
    }
    
    private function formatLeadRow($lead, $fields, $options)
    {
        $row = [];
        
        foreach ($fields as $field) {
            switch ($field) {
                case 'id':
                    $row[$field] = $lead->id;
                    break;
                case 'name':
                    $row[$field] = $lead->name;
                    break;
                case 'email':
                    $row[$field] = $lead->email;
                    break;
                case 'phone':
                    $row[$field] = $lead->phone;
                    break;
                case 'company':
                    $row[$field] = $lead->company;
                    break;
                case 'position':
                    $row[$field] = $lead->position;
                    break;
                case 'source':
                    $row[$field] = $lead->source->name ?? '';
                    break;
                case 'status':
                    $row[$field] = $lead->status->name ?? '';
                    break;
                case 'assigned_to':
                    $row[$field] = $lead->assignedUser->name ?? '';
                    break;
                case 'estimated_value':
                    $row[$field] = $lead->estimated_value;
                    break;
                case 'score':
                    $row[$field] = $lead->score;
                    break;
                case 'priority':
                    $row[$field] = $lead->priority;
                    break;
                case 'created_at':
                    $row[$field] = $lead->created_at->format('Y-m-d H:i:s');
                    break;
                case 'last_contact_at':
                    $row[$field] = $lead->last_contact_at?->format('Y-m-d H:i:s');
                    break;
                case 'converted_at':
                    $row[$field] = $lead->converted_at?->format('Y-m-d H:i:s');
                    break;
                case 'notes':
                    $row[$field] = $lead->notes;
                    break;
                default:
                    $row[$field] = $lead->{$field} ?? '';
            }
        }
        
        return $row;
    }
    
    private function formatActivities($activities)
    {
        return $activities->map(function($activity) {
            return [
                'type' => $activity->type,
                'description' => $activity->description,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
    
    private function formatNotes($notes)
    {
        return $notes->map(function($note) {
            return [
                'content' => $note->content,
                'created_at' => $note->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
    
    private function generateCSVFile($data, $filepath, $fields)
    {
        $handle = fopen($filepath, 'w');
        
        // Add BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");
        
        // Write headers
        fputcsv($handle, $fields);
        
        // Write data
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($fields as $field) {
                $value = $row[$field] ?? '';
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                $csvRow[] = $value;
            }
            fputcsv($handle, $csvRow);
        }
        
        fclose($handle);
    }
    
    private function generateExcelFile($data, $filepath, $fields)
    {
        // Implement Excel generation using Laravel Excel
        $excelData = [];
        
        // Add headers
        $excelData[] = $fields;
        
        // Add data
        foreach ($data as $row) {
            $excelRow = [];
            foreach ($fields as $field) {
                $value = $row[$field] ?? '';
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                $excelRow[] = $value;
            }
            $excelData[] = $excelRow;
        }
        
        Excel::store(new class($excelData) {
            private $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function collection()
            {
                return collect($this->data);
            }
        }, 'exports/' . basename($filepath));
    }
    
    private function generatePDFFile($data, $filepath, $fields)
    {
        // Implement PDF generation
        $html = view('exports.lead-pdf', compact('data', 'fields'))->render();
        
        // Use DOMPDF or similar library
        file_put_contents($filepath, $html);
    }
    
    private function updateExportStatus($exportId, $updates)
    {
        $exports = cache()->get('lead_exports', []);
        $exportIndex = collect($exports)->search(function($export) use ($exportId) {
            return $export['id'] == $exportId;
        });
        
        if ($exportIndex !== false) {
            $exports[$exportIndex] = array_merge($exports[$exportIndex], $updates);
            cache()->put('lead_exports', $exports, now()->addDays(30));
        }
    }
    
    private function exportCSV($data, $filename)
    {
        $handle = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");
        
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
            
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    private function exportExcel($data, $filename)
    {
        return Excel::download(new class($data) {
            private $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function collection()
            {
                return collect($this->data);
            }
            
            public function headings(): array
            {
                return !empty($this->data) ? array_keys($this->data[0]) : [];
            }
        }, $filename);
    }
}
