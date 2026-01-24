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
        $this->middleware('permission:manage-widgets');
    }

    public function index(): View
    {
        $widgets = Widget::latest()->get();

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

        $maxOrder = Widget::where('position', $validated['position'])->max('order') ?? 0;
        $validated['order'] = $validated['order'] ?? $maxOrder + 1;

        Widget::create($validated);

        return redirect()->route('admin.widgets.index')
            ->with('success', 'تم إنشاء الويدجت بنجاح');
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

    public function destroy(Widget $widget): RedirectResponse
    {
        $widget->delete();

        return redirect()->route('admin.widgets.index')
            ->with('success', 'تم حذف الويدجت بنجاح');
    }

    public function duplicate(Widget $widget): RedirectResponse
    {
        $newWidget = $widget->replicate();
        $newWidget->name = $widget->name . ' (نسخة)';
        $newWidget->order = Widget::where('position', $widget->position)->max('order') + 1;
        $newWidget->save();

        return redirect()->route('admin.widgets.edit', $newWidget)
            ->with('success', 'تم نسخ الويدجت بنجاح');
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

    public function toggleStatus(Widget $widget): RedirectResponse
    {
        $widget->update(['is_active' => !$widget->is_active]);

        return redirect()->route('admin.widgets.index')
            ->with('success', 'تم تحديث حالة الويدجت بنجاح');
    }

    public function preview(Widget $widget): View
    {
        return view('admin.widgets.preview', compact('widget'));
    }
}
