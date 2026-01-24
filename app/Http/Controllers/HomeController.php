<?php

namespace App\Http\Controllers;

use App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        // Get all properties first to debug
        $allProperties = Property::count();
        $activeProperties = Property::where('status', 'active')->count();
        
        \Log::info("Total Properties: {$allProperties}");
        \Log::info("Active Properties: {$activeProperties}");
        
        // Get featured properties for home page - show all active properties
        $featuredProperties = Property::where('status', 'active')
            ->with(['images'])
            ->latest()
            ->limit(6)
            ->get();

        // Get latest properties
        $latestProperties = Property::where('status', 'active')
            ->with(['images'])
            ->latest()
            ->limit(6)
            ->get();

        // Debug: Log the counts
        \Log::info('Featured Properties Count: ' . $featuredProperties->count());
        \Log::info('Latest Properties Count: ' . $latestProperties->count());

        // If no active properties, get all properties instead
        if ($featuredProperties->count() == 0) {
            $featuredProperties = Property::with(['images'])
                ->latest()
                ->limit(6)
                ->get();
            \Log::info('Using all properties instead, count: ' . $featuredProperties->count());
        }

        // Get properties by type for different sections
        $apartments = Property::where('status', 'active')
            ->where('property_type', 'apartment')
            ->with(['images'])
            ->limit(3)
            ->get();

        $villas = Property::where('status', 'active')
            ->where('property_type', 'villa')
            ->with(['images'])
            ->limit(3)
            ->get();

        $houses = Property::where('status', 'active')
            ->where('property_type', 'house')
            ->with(['images'])
            ->limit(3)
            ->get();

        return view('home', compact(
            'featuredProperties',
            'latestProperties',
            'apartments',
            'villas',
            'houses'
        ));
    }
}
