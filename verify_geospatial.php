<?php

use App\Models\Geospatial\GeospatialAnalytics;
use App\Models\Geospatial\Heatmap;
use App\Models\Geospatial\LocationIntelligence;

try {
    echo "Checking GeospatialAnalytics...\n";
    $totalDataPoints = GeospatialAnalytics::count();
    $accuracy = GeospatialAnalytics::avg('confidence_score') ?? 0;
    echo "Total Data Points: $totalDataPoints\n";
    echo "Accuracy: $accuracy\n";

    echo "Checking Heatmap...\n";
    $heatmapCount = Heatmap::count();
    $popDensity = Heatmap::where('heatmap_type', 'population')->count();
    echo "Heatmap Count: $heatmapCount\n";
    echo "Population Density Heatmaps: $popDensity\n";

    echo "Checking LocationIntelligence...\n";
    $liCount = LocationIntelligence::count();
    $resScore = LocationIntelligence::where('intelligence_type', 'residential')->avg('location_score') ?? 0;
    echo "LocationIntelligence Count: $liCount\n";
    echo "Residential Score: $resScore\n";

    echo "All checks passed successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
