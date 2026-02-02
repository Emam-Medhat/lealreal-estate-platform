<?php

namespace App\Http\Controllers;

use App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        // Check if user is authenticated
        if (auth()->check()) {
            // Redirect authenticated users to dashboard
            return redirect()->route('dashboard');
        }

        // For non-authenticated users, show landing page
        // Get featured properties for home page - show all active properties
        $featuredProperties = Property::where('status', 'active')
            ->with(['media', 'propertyType'])
            ->latest()
            ->limit(6)
            ->get();

        // Get latest properties
        $latestProperties = Property::where('status', 'active')
            ->with(['media', 'propertyType'])
            ->latest()
            ->limit(6)
            ->get();

        // If no active properties, get all properties instead
        if ($featuredProperties->count() == 0) {
            $featuredProperties = Property::with(['media', 'propertyType'])
                ->latest()
                ->limit(6)
                ->get();
        }

        // Get properties by type for different sections
        $apartments = Property::where('status', 'active')
            ->whereHas('propertyType', function($q) {
                $q->where('slug', 'apartment');
            })
            ->with(['media', 'propertyType'])
            ->limit(3)
            ->get();

        $villas = Property::where('status', 'active')
            ->whereHas('propertyType', function($q) {
                $q->where('slug', 'villa');
            })
            ->with(['media', 'propertyType'])
            ->limit(3)
            ->get();

        $houses = Property::where('status', 'active')
            ->whereHas('propertyType', function($q) {
                $q->where('slug', 'house');
            })
            ->with(['media', 'propertyType'])
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
