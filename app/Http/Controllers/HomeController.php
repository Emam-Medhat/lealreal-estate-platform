<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\PropertyRepositoryInterface;

class HomeController extends Controller
{
    protected $propertyRepository;

    public function __construct(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function index()
    {
        // Check if user is authenticated
        if (auth()->check()) {
            // Redirect authenticated users to dashboard
            return redirect()->route('dashboard');
        }

        // For non-authenticated users, show landing page
        // Get featured properties for home page - show all active properties
        $featuredProperties = $this->propertyRepository->getFeatured(6);

        // Get latest properties
        $latestProperties = $this->propertyRepository->getLatestActive(6);

        // Get properties by type for different sections
        $apartments = $this->propertyRepository->getByTypeSlug('apartment', 3);
        $villas = $this->propertyRepository->getByTypeSlug('villa', 3);
        $houses = $this->propertyRepository->getByTypeSlug('house', 3);

        return view('home', compact(
            'featuredProperties',
            'latestProperties',
            'apartments',
            'villas',
            'houses'
        ));
    }
}
