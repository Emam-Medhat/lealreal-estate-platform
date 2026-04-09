<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionFeatureController extends Controller
{
    public function index()
    {
        $features = SubscriptionFeature::with(['plans'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20);

        $categories = SubscriptionFeature::distinct()->pluck('category')->filter();

        return view('subscriptions.features.index', compact('features', 'categories'));
    }

    public function create()
    {
        return view('subscriptions.features.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'type' => 'required|in:boolean,numeric,limited,unlimited',
            'unit' => 'nullable|string|max:50',
            'default_value' => 'nullable|numeric',
            'is_included_in_free' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            $feature = SubscriptionFeature::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'category' => $validated['category'],
                'icon' => $validated['icon'] ?? 'fas fa-check',
                'type' => $validated['type'],
                'unit' => $validated['unit'],
                'default_value' => $validated['default_value'] ?? 0,
                'is_included_in_free' => $validated['is_included_in_free'] ?? false,
                'is_required' => $validated['is_required'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0
            ]);

            return redirect()->route('subscriptions.features.show', $feature)
                ->with('success', 'Subscription feature created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create subscription feature: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SubscriptionFeature $feature)
    {
        $feature->load(['plans' => function($query) {
            $query->where('is_active', true);
        }]);

        $stats = [
            'plans_count' => $feature->plans()->where('is_active', true)->count(),
            'active_plans' => $feature->plans()->where('is_active', true)->count(),
            'usage_count' => $this->getFeatureUsageCount($feature)
        ];

        return view('subscriptions.features.show', compact('feature', 'stats'));
    }

    public function edit(SubscriptionFeature $feature)
    {
        return view('subscriptions.features.edit', compact('feature'));
    }

    public function update(Request $request, SubscriptionFeature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'type' => 'required|in:boolean,numeric,limited,unlimited',
            'unit' => 'nullable|string|max:50',
            'default_value' => 'nullable|numeric',
            'is_included_in_free' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            $feature->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'category' => $validated['category'],
                'icon' => $validated['icon'] ?? 'fas fa-check',
                'type' => $validated['type'],
                'unit' => $validated['unit'],
                'default_value' => $validated['default_value'] ?? 0,
                'is_included_in_free' => $validated['is_included_in_free'] ?? false,
                'is_required' => $validated['is_required'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0
            ]);

            return redirect()->route('subscriptions.features.show', $feature)
                ->with('success', 'Subscription feature updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update subscription feature: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SubscriptionFeature $feature)
    {
        if ($feature->plans()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete feature that is assigned to plans.');
        }

        try {
            $feature->delete();

            return redirect()->route('subscriptions.features.index')
                ->with('success', 'Subscription feature deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete subscription feature: ' . $e->getMessage());
        }
    }

    public function toggleStatus(SubscriptionFeature $feature)
    {
        $feature->update([
            'is_active' => !$feature->is_active
        ]);

        $status = $feature->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Subscription feature {$status} successfully.");
    }

    public function getFeaturesByCategory()
    {
        $features = SubscriptionFeature::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return response()->json($features);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $features = SubscriptionFeature::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($features);
    }

    private function getFeatureUsageCount(SubscriptionFeature $feature)
    {
        // This would count how many times this feature is used across active subscriptions
        // Implementation depends on your usage tracking system
        return 0;
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'features' => 'required|array',
            'features.*.id' => 'required|exists:subscription_features,id',
            'features.*.sort_order' => 'required|integer|min:0',
            'features.*.is_active' => 'required|boolean'
        ]);

        try {
            foreach ($validated['features'] as $featureData) {
                $feature = SubscriptionFeature::find($featureData['id']);
                $feature->update([
                    'sort_order' => $featureData['sort_order'],
                    'is_active' => $featureData['is_active']
                ]);
            }

            return redirect()->back()
                ->with('success', 'Features updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update features: ' . $e->getMessage());
        }
    }
}
