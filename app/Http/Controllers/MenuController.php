<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-menus');
    }

    public function index(): View
    {
        $menus = Menu::withCount('items')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create(): View
    {
        return view('admin.menus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus',
            'description' => 'nullable|string',
            'location' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Menu::create($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'تم إنشاء القائمة بنجاح');
    }

    public function show(Menu $menu): View
    {
        $menu->load(['items' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.menus.show', compact('menu'));
    }

    public function edit(Menu $menu): View
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug,'.$menu->getKey(),
            'description' => 'nullable|string',
            'location' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $menu->update($validated);

        return redirect()->route('admin.menus.show', $menu)
            ->with('success', 'تم تحديث القائمة بنجاح');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->items()->delete();
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'تم حذف القائمة بنجاح');
    }

    public function builder(Menu $menu): View
    {
        $menu->load(['items' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.menus.builder', compact('menu'));
    }

    public function addItem(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'nullable|integer|min:0',
            'target' => 'nullable|in:_blank,_self,_parent,_top',
            'icon_class' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $maxOrder = MenuItem::where('menu_id', $menu->id)
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order') ?? 0;

        $validated['order'] = $validated['order'] ?? $maxOrder + 1;

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => $validated['title'],
            'url' => $validated['url'],
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => $validated['order'],
            'target' => $validated['target'] ?? '_self',
            'icon_class' => $validated['icon_class'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.menus.builder', $menu)
            ->with('success', 'تم إضافة عنصر القائمة بنجاح');
    }

    public function updateItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'nullable|integer|min:0',
            'target' => 'nullable|in:_blank,_self,_parent,_top',
            'icon_class' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $menuItem->update($validated);

        return redirect()->route('admin.menus.builder', $menuItem->menu)
            ->with('success', 'تم تحديث عنصر القائمة بنجاح');
    }

    public function deleteItem(MenuItem $menuItem): RedirectResponse
    {
        $menu = $menuItem->menu;
        
        // Delete child items first
        $menuItem->children()->delete();
        $menuItem->delete();

        return redirect()->route('admin.menus.builder', $menu)
            ->with('success', 'تم حذف عنصر القائمة بنجاح');
    }

    public function reorderItems(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $itemData) {
            MenuItem::where('id', $itemData['id'])->update([
                'parent_id' => $itemData['parent_id'],
                'order' => $itemData['order'],
            ]);
        }

        return redirect()->route('admin.menus.builder', $menu)
            ->with('success', 'تم إعادة ترتيب عناصر القائمة بنجاح');
    }
}
