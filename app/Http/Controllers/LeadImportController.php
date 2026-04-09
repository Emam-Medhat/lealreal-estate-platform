<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LeadImportController extends Controller
{
    public function index()
    {
        $imports = cache()->get('lead_imports', []);
        
        return view('lead-import.index', compact('imports'));
    }
    
    public function create()
    {
        $sources = LeadSource::all();
        $statuses = LeadStatus::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-import.create', compact('sources', 'statuses', 'users'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'source_id' => 'nullable|exists:lead_sources,id',
            'status_id' => 'nullable|exists:lead_statuses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'mapping' => 'required|array',
            'mapping.*' => 'required|string',
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean',
        ]);
        
        try {
            $file = $request->file('file');
            $importId = time();
            
            // Store import info
            $import = [
                'id' => $importId,
                'filename' => $file->getClientOriginalName(),
                'status' => 'processing',
                'total_rows' => 0,
                'imported_rows' => 0,
                'failed_rows' => 0,
                'errors' => [],
                'created_by' => auth()->id(),
                'created_at' => now(),
            ];
            
            $imports = cache()->get('lead_imports', []);
            $imports[] = $import;
            cache()->put('lead_imports', $imports, now()->addDays(30));
            
            // Process import in background
            $this->processImport($file, $request->all(), $importId);
            
            return redirect()->route('lead-import.show', $importId)
                ->with('success', 'تم بدء عملية استيراد العملاء المحتملين');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }
    
    public function show($importId)
    {
        $imports = cache()->get('lead_imports', []);
        $import = collect($imports)->firstWhere('id', $importId);
        
        if (!$import) {
            abort(404);
        }
        
        return view('lead-import.show', compact('import'));
    }
    
    public function downloadTemplate()
    {
        $templateData = [
            ['name', 'email', 'phone', 'company', 'position', 'estimated_value', 'notes'],
            ['أحمد محمد', 'ahmed@example.com', '0501234567', 'شركة النمو', 'مدير', '100000', 'عميل مهتم'],
            ['فاطمة علي', 'fatima@example.com', '0507654321', 'شركة المستقبل', 'مديرة مالية', '150000', 'عميل محتمل قوي'],
        ];
        
        $filename = 'lead_import_template.csv';
        $handle = fopen($filename, 'w');
        
        // Add BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");
        
        foreach ($templateData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        return response()->download($filename)->deleteFileAfterSend(true);
    }
    
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);
        
        $file = $request->file('file');
        $data = $this->readFileData($file);
        
        return response()->json([
            'headers' => array_keys($data[0] ?? []),
            'sample_data' => array_slice($data, 0, 5),
            'total_rows' => count($data),
        ]);
    }
    
    public function validateMapping(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'mapping' => 'required|array',
        ]);
        
        $file = $request->file('file');
        $data = $this->readFileData($file);
        $mapping = $request->mapping;
        
        $errors = [];
        $requiredFields = ['name', 'email'];
        
        foreach ($requiredFields as $field) {
            if (!in_array($field, $mapping)) {
                $errors[] = "الحقل {$field} مطلوب";
            }
        }
        
        // Validate data format
        $sampleRow = $data[0] ?? [];
        foreach ($mapping as $dbField => $csvField) {
            if (isset($sampleRow[$csvField])) {
                $value = $sampleRow[$csvField];
                
                if ($dbField === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "تنسيق البريد الإلكتروني غير صحيح في الحقل {$csvField}";
                }
                
                if ($dbField === 'estimated_value' && !is_numeric($value)) {
                    $errors[] = "القيمة المقدرة يجب أن تكون رقمية في الحقل {$csvField}";
                }
            }
        }
        
        return response()->json(['errors' => $errors]);
    }
    
    private function processImport($file, $options, $importId)
    {
        try {
            $data = $this->readFileData($file);
            $mapping = $options['mapping'];
            $skipDuplicates = $options['skip_duplicates'] ?? false;
            $updateExisting = $options['update_existing'] ?? false;
            
            $importedCount = 0;
            $failedCount = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($data as $index => $row) {
                try {
                    $leadData = $this->mapRowToLeadData($row, $mapping, $options);
                    
                    // Check for duplicates
                    if ($skipDuplicates) {
                        $existingLead = Lead::where('email', $leadData['email'])->first();
                        if ($existingLead) {
                            if ($updateExisting) {
                                $existingLead->update($leadData);
                                $importedCount++;
                            }
                            continue;
                        }
                    }
                    
                    $lead = Lead::create($leadData);
                    
                    // Create activity
                    LeadActivity::create([
                        'lead_id' => $lead->id,
                        'type' => 'imported',
                        'description' => 'تم استيراد العميل المحتمل',
                        'user_id' => auth()->id(),
                    ]);
                    
                    $importedCount++;
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "صف " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Update import status
            $this->updateImportStatus($importId, [
                'status' => 'completed',
                'imported_rows' => $importedCount,
                'failed_rows' => $failedCount,
                'errors' => $errors,
                'completed_at' => now(),
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            $this->updateImportStatus($importId, [
                'status' => 'failed',
                'errors' => [$e->getMessage()],
                'completed_at' => now(),
            ]);
        }
    }
    
    private function readFileData($file)
    {
        $extension = $file->getClientOriginalExtension();
        
        if ($extension === 'csv') {
            return $this->readCSVFile($file);
        } else {
            return $this->readExcelFile($file);
        }
    }
    
    private function readCSVFile($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle) {
            $headers = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($headers, $row);
            }
            
            fclose($handle);
        }
        
        return $data;
    }
    
    private function readExcelFile($file)
    {
        $data = [];
        $spreadsheet = Excel::toArray([], $file);
        
        if (!empty($spreadsheet[0])) {
            $headers = $spreadsheet[0][0];
            
            for ($i = 1; $i < count($spreadsheet[0]); $i++) {
                $data[] = array_combine($headers, $spreadsheet[0][$i]);
            }
        }
        
        return $data;
    }
    
    private function mapRowToLeadData($row, $mapping, $options)
    {
        $leadData = [
            'created_by' => auth()->id(),
        ];
        
        foreach ($mapping as $dbField => $csvField) {
            if (isset($row[$csvField])) {
                $value = trim($row[$csvField]);
                
                if ($value !== '') {
                    $leadData[$dbField] = $value;
                }
            }
        }
        
        // Set default values from options
        if (isset($options['source_id'])) {
            $leadData['source_id'] = $options['source_id'];
        }
        
        if (isset($options['status_id'])) {
            $leadData['status_id'] = $options['status_id'];
        }
        
        if (isset($options['assigned_to'])) {
            $leadData['assigned_to'] = $options['assigned_to'];
        }
        
        return $leadData;
    }
    
    private function updateImportStatus($importId, $updates)
    {
        $imports = cache()->get('lead_imports', []);
        $importIndex = collect($imports)->search(function($import) use ($importId) {
            return $import['id'] == $importId;
        });
        
        if ($importIndex !== false) {
            $imports[$importIndex] = array_merge($imports[$importIndex], $updates);
            cache()->put('lead_imports', $imports, now()->addDays(30));
        }
    }
}
