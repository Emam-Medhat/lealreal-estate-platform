<?php

namespace App\Http\Controllers;

use App\Models\ContractTemplate;
use App\Models\ContractTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractTemplateController extends Controller
{
    public function index()
    {
        $templates = ContractTemplate::with(['terms', 'category'])
            ->filter(request(['search', 'category', 'type', 'status']))
            ->orderBy('name')
            ->paginate(20);
            
        $categories = \App\Models\DocumentCategory::all();
        
        return view('contracts.templates.index', compact('templates', 'categories'));
    }
    
    public function create()
    {
        $categories = \App\Models\DocumentCategory::all();
        
        return view('contracts.templates.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'type' => 'required|in:rental,sale,partnership,service,employment,other',
            'content' => 'required|string',
            'terms' => 'required|array|min:1',
            'terms.*.title' => 'required|string|max:255',
            'terms.*.content' => 'required|string',
            'terms.*.order' => 'required|integer|min:1',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $template = ContractTemplate::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'content' => $request->content,
                'variables' => $request->variables ?? [],
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
            ]);
            
            // Create terms
            foreach ($request->terms as $termData) {
                $template->terms()->create([
                    'title' => $termData['title'],
                    'content' => $termData['content'],
                    'order' => $termData['order'],
                    'is_required' => $termData['is_required'] ?? false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('contracts.templates.show', $template)
                ->with('success', 'تم إنشاء قالب العقد بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء قالب العقد');
        }
    }
    
    public function show(ContractTemplate $template)
    {
        $template->load(['terms' => function($query) {
            $query->orderBy('order');
        }, 'category', 'createdBy']);
        
        $contracts = $template->contracts()
            ->with(['parties'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('contracts.templates.show', compact('template', 'contracts'));
    }
    
    public function edit(ContractTemplate $template)
    {
        $template->load(['terms' => function($query) {
            $query->orderBy('order');
        }]);
        
        $categories = \App\Models\DocumentCategory::all();
        
        return view('contracts.templates.edit', compact('template', 'categories'));
    }
    
    public function update(Request $request, ContractTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'type' => 'required|in:rental,sale,partnership,service,employment,other',
            'content' => 'required|string',
            'terms' => 'required|array|min:1',
            'terms.*.title' => 'required|string|max:255',
            'terms.*.content' => 'required|string',
            'terms.*.order' => 'required|integer|min:1',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $template->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'content' => $request->content,
                'variables' => $request->variables ?? [],
                'is_active' => $request->boolean('is_active', true),
            ]);
            
            // Update terms
            $template->terms()->delete();
            
            foreach ($request->terms as $termData) {
                $template->terms()->create([
                    'title' => $termData['title'],
                    'content' => $termData['content'],
                    'order' => $termData['order'],
                    'is_required' => $termData['is_required'] ?? false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('contracts.templates.show', $template)
                ->with('success', 'تم تحديث قالب العقد بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث قالب العقد');
        }
    }
    
    public function destroy(ContractTemplate $template)
    {
        // Check if template is used in contracts
        if ($template->contracts()->exists()) {
            return back()->with('error', 'لا يمكن حذف القالب المستخدم في العقود');
        }
        
        $template->delete();
        
        return redirect()->route('contracts.templates.index')
            ->with('success', 'تم حذف قالب العقد بنجاح');
    }
    
    public function duplicate(ContractTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (نسخة)';
        $newTemplate->slug = Str::slug($newTemplate->name);
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();
        
        // Duplicate terms
        foreach ($template->terms as $term) {
            $newTerm = $term->replicate();
            $newTerm->contract_template_id = $newTemplate->id;
            $newTerm->save();
        }
        
        return redirect()->route('contracts.templates.edit', $newTemplate)
            ->with('success', 'تم نسخ قالب العقد بنجاح');
    }
    
    public function toggleStatus(ContractTemplate $template)
    {
        $template->update([
            'is_active' => !$template->is_active
        ]);
        
        $status = $template->is_active ? 'تفعيل' : 'تعطيل';
        
        return back()->with('success', 'تم ' . $status . ' قالب العقد بنجاح');
    }
    
    public function preview(ContractTemplate $template)
    {
        $template->load(['terms' => function($query) {
            $query->orderBy('order');
        }]);
        
        return view('contracts.templates.preview', compact('template'));
    }
    
    public function createContract(ContractTemplate $template)
    {
        $template->load(['terms' => function($query) {
            $query->orderBy('order');
        }]);
        
        return view('contracts.create-from-template', compact('template'));
    }
    
    public function export(ContractTemplate $template)
    {
        $template->load(['terms' => function($query) {
            $query->orderBy('order');
        }]);
        
        $pdf = \PDF::loadView('contracts.templates.export', compact('template'));
        
        return $pdf->download('contract-template-' . $template->slug . '.pdf');
    }
    
    public function analytics()
    {
        $templates = ContractTemplate::withCount(['contracts' => function($query) {
                $query->where('created_at', '>=', now()->subMonths(12));
            }])
            ->orderBy('contracts_count', 'desc')
            ->take(10)
            ->get();
            
        $typeStats = ContractTemplate::select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();
            
        $recentTemplates = ContractTemplate::with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('contracts.templates.analytics', compact('templates', 'typeStats', 'recentTemplates'));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $templates = ContractTemplate::with(['category'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(20)
            ->get();
            
        return response()->json([
            'templates' => $templates
        ]);
    }
    
    public function variables(ContractTemplate $template)
    {
        return response()->json([
            'variables' => $template->variables ?? []
        ]);
    }
    
    public function validateTemplate(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);
        
        $content = $request->content;
        $variables = $request->variables ?? [];
        $errors = [];
        
        // Check for missing variables
        preg_match_all('/\{\{(.*?)\}\}/', $content, $matches);
        
        foreach ($matches[1] as $variable) {
            if (!in_array($variable, $variables)) {
                $errors[] = "المتغير {{{$variable}}} غير معرف في قائمة المتغيرات";
            }
        }
        
        // Check for unused variables
        foreach ($variables as $variable) {
            if (!preg_match("/\{\{{$variable}\}\}/", $content)) {
                $errors[] = "المتغير {$variable} غير مستخدم في المحتوى";
            }
        }
        
        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors
        ]);
    }
}
