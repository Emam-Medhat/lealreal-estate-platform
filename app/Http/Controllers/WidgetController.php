<?php

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission middleware for debugging
        // $this->middleware('permission:manage-widgets');
    }

    public function index(): View
    {
        $widgets = Widget::latest()->paginate(12);

        return view('admin.widgets.index', compact('widgets'));
    }

    public function create(): View
    {
        $widgetTypes = [
            'text' => 'نص',
            'html' => 'HTML',
            'image' => 'صورة',
            'video' => 'فيديو',
            'contact_form' => 'نموذج تواصل',
            'social_links' => 'روابط التواصل الاجتماعي',
            'recent_posts' => 'المقالات الأخيرة',
            'categories' => 'التصنيفات',
            'tags' => 'الوسوم',
            'search' => 'بحث',
            'custom' => 'مخصص',
        ];

        $positions = [
            'header' => 'رأس الصفحة',
            'sidebar_left' => 'الشريط الجانبي الأيسر',
            'sidebar_right' => 'الشريط الجانبي الأيمن',
            'footer' => 'تذييل الصفحة',
            'content_top' => 'أعلى المحتوى',
            'content_bottom' => 'أسفل المحتوى',
        ];

        return view('admin.widgets.create', compact('widgetTypes', 'positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Debug: Log the incoming request
        \Log::info('Widget creation attempt', [
            'request_data' => $request->all(),
            'config_value' => $request->input('config'),
            'config_is_empty' => empty($request->input('config')),
            'config_trimmed' => trim($request->input('config', '')),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:widgets',
            'type' => 'required|string|max:255',
            'content' => 'nullable',
            'location' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        // Handle config JSON - make it truly optional
        $configValue = $request->input('config');
        
        if (!empty($configValue) && trim($configValue) !== '') {
            \Log::info('Processing config JSON', ['config_value' => $configValue]);
            
            $configData = json_decode($configValue, true);
            $jsonError = json_last_error();
            
            if ($jsonError !== JSON_ERROR_NONE) {
                \Log::error('JSON decode error', [
                    'error_code' => $jsonError,
                    'error_msg' => json_last_error_msg(),
                    'config_value' => $configValue
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Invalid JSON in configuration field. Error: ' . json_last_error_msg() . '. Please check your JSON syntax or leave the field empty.');
            }
            $validated['config'] = $configData;
        } else {
            $validated['config'] = null;
            \Log::info('Config field is empty or null');
        }

        // Handle boolean field
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        // Ensure sort_order has a default value
        if (!isset($validated['sort_order']) || $validated['sort_order'] === '') {
            $validated['sort_order'] = 0;
        }

        \Log::info('Creating widget with data', ['validated' => $validated]);

        $widget = Widget::create($validated);

        return redirect()->route('admin.widgets.index')
            ->with('success', 'Widget created successfully');
    }

    public function show(Widget $widget): View
    {
        return view('admin.widgets.show', compact('widget'));
    }

    public function edit(Widget $widget): View
    {
        $widgetTypes = [
            'text' => 'نص',
            'html' => 'HTML',
            'image' => 'صورة',
            'video' => 'فيديو',
            'contact_form' => 'نموذج تواصل',
            'social_links' => 'روابط التواصل الاجتماعي',
            'recent_posts' => 'المقالات الأخيرة',
            'categories' => 'التصنيفات',
            'tags' => 'الوسوم',
            'search' => 'بحث',
            'custom' => 'مخصص',
        ];

        $positions = [
            'header' => 'رأس الصفحة',
            'sidebar_left' => 'الشريط الجانبي الأيسر',
            'sidebar_right' => 'الشريط الجانبي الأيمن',
            'footer' => 'تذييل الصفحة',
            'content_top' => 'أعلى المحتوى',
            'content_bottom' => 'أسفل المحتوى',
        ];

        return view('admin.widgets.edit', compact('widget', 'widgetTypes', 'positions'));
    }

    public function update(Request $request, Widget $widget): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable',
            'config' => 'nullable|array',
            'css_class' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $widget->update($validated);

        return redirect()->route('admin.widgets.show', $widget)
            ->with('success', 'تم تحديث الويدجت بنجاح');
    }

    public function destroy(Widget $widget): JsonResponse
    {
        $widget->delete();

        return response()->json([
            'success' => true,
            'message' => 'Widget deleted successfully'
        ]);
    }

    public function duplicate(Widget $widget): JsonResponse
    {
        $newWidget = $widget->replicate();
        $newWidget->title = $widget->title . ' (Copy)';
        $newWidget->slug = $widget->slug . '-copy-' . time();
        $newWidget->sort_order = Widget::where('location', $widget->location)->max('sort_order') + 1;
        $newWidget->save();

        return response()->json([
            'success' => true,
            'message' => 'Widget duplicated successfully',
            'widget_id' => $newWidget->id
        ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*.id' => 'required|exists:widgets,id',
            'widgets.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['widgets'] as $widgetData) {
            Widget::where('id', $widgetData['id'])->update(['order' => $widgetData['order']]);
        }

        return redirect()->route('admin.widgets.index')
            ->with('success', 'تم إعادة ترتيب الويدجتات بنجاح');
    }

    public function toggleStatus(Widget $widget): JsonResponse
    {
        try {
            // Simple toggle without logging for debugging
            $widget->update(['is_active' => !$widget->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Widget status updated successfully',
                'new_status' => $widget->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkToggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*' => 'required|exists:widgets,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $isActive = $validated['action'] === 'activate';
        
        Widget::whereIn('id', $validated['widgets'])
            ->update(['is_active' => $isActive]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*' => 'required|exists:widgets,id',
        ]);

        Widget::whereIn('id', $validated['widgets'])->delete();

        return response()->json(['success' => true]);
    }

    public function preview(Widget $widget): View
    {
        return view('admin.widgets.preview', compact('widget'));
    }
}
