<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Geospatial\GeospatialAnalyticsController;
use App\Http\Controllers\Geospatial\HeatmapController;
use App\Http\Controllers\Geospatial\LocationIntelligenceController;
use App\Http\Controllers\Geospatial\ProximityAnalysisController;
use App\Http\Controllers\Geospatial\DemographicAnalysisController;
use App\Http\Controllers\Geospatial\PropertyDensityController;
use App\Http\Controllers\Geospatial\WalkScoreController;
use App\Http\Controllers\Geospatial\TransitScoreController;
use App\Http\Controllers\Geospatial\SchoolDistrictController;
use App\Http\Controllers\Geospatial\CrimeMapController;
use App\Http\Controllers\Geospatial\FloodRiskController;
use App\Http\Controllers\Geospatial\EarthquakeRiskController;
use App\Http\Controllers\Geospatial\PropertyAppreciationMapController;

/*
|--------------------------------------------------------------------------
| Geospatial Analytics Routes
|--------------------------------------------------------------------------
|
| Routes for geospatial analytics and spatial analysis features
|
*/

Route::middleware(['web', 'auth'])->prefix('geospatial')->name('geospatial.')->group(function () {
    
    // Main Geospatial Analytics Routes
    Route::get('/', [GeospatialAnalyticsController::class, 'index'])->name('index');
    Route::get('/analytics', [GeospatialAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/create', [GeospatialAnalyticsController::class, 'create'])->name('analytics.create');
    Route::post('/analytics', [GeospatialAnalyticsController::class, 'store'])->name('analytics.store');
    Route::get('/analytics/{geospatialAnalytics}', [GeospatialAnalyticsController::class, 'show'])->name('analytics.show');
    Route::get('/analytics/{geospatialAnalytics}/edit', [GeospatialAnalyticsController::class, 'edit'])->name('analytics.edit');
    Route::put('/analytics/{geospatialAnalytics}', [GeospatialAnalyticsController::class, 'update'])->name('analytics.update');
    Route::delete('/analytics/{geospatialAnalytics}', [GeospatialAnalyticsController::class, 'destroy'])->name('analytics.destroy');
    
    // Geospatial Analytics API Routes
    Route::get('/analytics/overview', [GeospatialAnalyticsController::class, 'overview'])->name('analytics.overview');
    Route::get('/analytics/area/{latitude}/{longitude}', [GeospatialAnalyticsController::class, 'getAreaAnalytics'])->name('analytics.area');
    Route::post('/analytics/export', [GeospatialAnalyticsController::class, 'export'])->name('analytics.export');
    Route::post('/analytics/batch-analyze', [GeospatialAnalyticsController::class, 'batchAnalyze'])->name('analytics.batch_analyze');
    Route::post('/analytics/batch-delete', [GeospatialAnalyticsController::class, 'batchDelete'])->name('analytics.batch_delete');
    
    // Specific Analysis Types
    Route::get('/analytics/market-trend', [GeospatialAnalyticsController::class, 'marketTrendAnalysis'])->name('analytics.market_trend');
    Route::get('/analytics/price-appreciation', [GeospatialAnalyticsController::class, 'priceAppreciationAnalysis'])->name('analytics.price_appreciation');
    Route::get('/analytics/investment-potential', [GeospatialAnalyticsController::class, 'investmentPotentialAnalysis'])->name('analytics.investment_potential');
    Route::get('/analytics/risk-assessment', [GeospatialAnalyticsController::class, 'riskAssessment'])->name('analytics.risk_assessment');
    Route::get('/analytics/demographic', [GeospatialAnalyticsController::class, 'demographicAnalysis'])->name('analytics.demographic');
    Route::get('/analytics/accessibility', [GeospatialAnalyticsController::class, 'accessibilityAnalysis'])->name('analytics.accessibility');
    Route::get('/analytics/environmental-impact', [GeospatialAnalyticsController::class, 'environmentalImpactAnalysis'])->name('analytics.environmental_impact');

    // Heatmap Routes
    Route::get('/heatmaps', [HeatmapController::class, 'index'])->name('heatmaps.index');
    Route::get('/heatmaps/create', [HeatmapController::class, 'create'])->name('heatmaps.create');
    Route::post('/heatmaps', [HeatmapController::class, 'store'])->name('heatmaps.store');
    Route::get('/heatmaps/{heatmap}', [HeatmapController::class, 'show'])->name('heatmaps.show');
    Route::get('/heatmaps/{heatmap}/edit', [HeatmapController::class, 'edit'])->name('heatmaps.edit');
    Route::put('/heatmaps/{heatmap}', [HeatmapController::class, 'update'])->name('heatmaps.update');
    Route::delete('/heatmaps/{heatmap}', [HeatmapController::class, 'destroy'])->name('heatmaps.destroy');
    
    // Heatmap API Routes
    Route::get('/heatmaps/data', [HeatmapController::class, 'getHeatmapData'])->name('heatmaps.data');
    Route::get('/heatmaps/statistics', [HeatmapController::class, 'getHeatmapStatistics'])->name('heatmaps.statistics');
    Route::post('/heatmaps/export', [HeatmapController::class, 'export'])->name('heatmaps.export');
    Route::get('/heatmaps/compare', [HeatmapController::class, 'compareHeatmaps'])->name('heatmaps.compare');
    
    // Specific Heatmap Types
    Route::get('/heatmaps/price-density', [HeatmapController::class, 'priceDensityHeatmap'])->name('heatmaps.price_density');
    Route::get('/heatmaps/price-appreciation', [HeatmapController::class, 'priceAppreciationHeatmap'])->name('heatmaps.price_appreciation');
    Route::get('/heatmaps/investment-hotspot', [HeatmapController::class, 'investmentHotspotHeatmap'])->name('heatmaps.investment_hotspot');
    Route::get('/heatmaps/risk-assessment', [HeatmapController::class, 'riskAssessmentHeatmap'])->name('heatmaps.risk_assessment');
    Route::get('/heatmaps/market-activity', [HeatmapController::class, 'marketActivityHeatmap'])->name('heatmaps.market_activity');
    Route::get('/heatmaps/demand-supply', [HeatmapController::class, 'demandSupplyHeatmap'])->name('heatmaps.demand_supply');
    Route::get('/heatmaps/accessibility', [HeatmapController::class, 'accessibilityHeatmap'])->name('heatmaps.accessibility');
    Route::get('/heatmaps/development-potential', [HeatmapController::class, 'developmentPotentialHeatmap'])->name('heatmaps.development_potential');

    // Location Intelligence Routes
    Route::get('/location-intelligence', [LocationIntelligenceController::class, 'index'])->name('location_intelligence.index');
    Route::get('/location-intelligence/create', [LocationIntelligenceController::class, 'create'])->name('location_intelligence.create');
    Route::post('/location-intelligence', [LocationIntelligenceController::class, 'store'])->name('location_intelligence.store');
    Route::get('/location-intelligence/{locationIntelligence}', [LocationIntelligenceController::class, 'show'])->name('location_intelligence.show');
    Route::get('/location-intelligence/{locationIntelligence}/edit', [LocationIntelligenceController::class, 'edit'])->name('location_intelligence.edit');
    Route::put('/location-intelligence/{locationIntelligence}', [LocationIntelligenceController::class, 'update'])->name('location_intelligence.update');
    Route::delete('/location-intelligence/{locationIntelligence}', [LocationIntelligenceController::class, 'destroy'])->name('location_intelligence.destroy');
    
    // Location Intelligence API Routes
    Route::get('/location-intelligence/area/{latitude}/{longitude}', [LocationIntelligenceController::class, 'getLocationIntelligence'])->name('location_intelligence.area');
    Route::get('/location-intelligence/competitive', [LocationIntelligenceController::class, 'getCompetitiveAnalysis'])->name('location_intelligence.competitive');
    Route::get('/location-intelligence/market', [LocationIntelligenceController::class, 'getMarketIntelligence'])->name('location_intelligence.market');
    Route::get('/location-intelligence/investment', [LocationIntelligenceController::class, 'getInvestmentIntelligence'])->name('location_intelligence.investment');
    Route::post('/location-intelligence/export', [LocationIntelligenceController::class, 'export'])->name('location_intelligence.export');
    
    // Specific Intelligence Types
    Route::get('/location-intelligence/market', [LocationIntelligenceController::class, 'marketIntelligence'])->name('location_intelligence.market_intelligence');
    Route::get('/location-intelligence/competitive', [LocationIntelligenceController::class, 'competitiveIntelligence'])->name('location_intelligence.competitive_intelligence');
    Route::get('/location-intelligence/location-score', [LocationIntelligenceController::class, 'locationScore'])->name('location_intelligence.location_score');
    Route::get('/location-intelligence/investment', [LocationIntelligenceController::class, 'investmentIntelligence'])->name('location_intelligence.investment_intelligence');
    Route::get('/location-intelligence/demographic', [LocationIntelligenceController::class, 'demographicIntelligence'])->name('location_intelligence.demographic_intelligence');
    Route::get('/location-intelligence/infrastructure', [LocationIntelligenceController::class, 'infrastructureIntelligence'])->name('location_intelligence.infrastructure_intelligence');
    Route::get('/location-intelligence/amenity', [LocationIntelligenceController::class, 'amenityIntelligence'])->name('location_intelligence.amenity_intelligence');
    Route::get('/location-intelligence/future-growth', [LocationIntelligenceController::class, 'futureGrowthIntelligence'])->name('location_intelligence.future_growth_intelligence');

    // Proximity Analysis Routes
    Route::get('/proximity-analysis', [ProximityAnalysisController::class, 'index'])->name('proximity_analysis.index');
    Route::get('/proximity-analysis/create', [ProximityAnalysisController::class, 'create'])->name('proximity_analysis.create');
    Route::post('/proximity-analysis', [ProximityAnalysisController::class, 'store'])->name('proximity_analysis.store');
    Route::get('/proximity-analysis/{proximityAnalysis}', [ProximityAnalysisController::class, 'show'])->name('proximity_analysis.show');
    Route::get('/proximity-analysis/{proximityAnalysis}/edit', [ProximityAnalysisController::class, 'edit'])->name('proximity_analysis.edit');
    Route::put('/proximity-analysis/{proximityAnalysis}', [ProximityAnalysisController::class, 'update'])->name('proximity_analysis.update');
    Route::delete('/proximity-analysis/{proximityAnalysis}', [ProximityAnalysisController::class, 'destroy'])->name('proximity_analysis.destroy');
    
    // Proximity Analysis API Routes
    Route::get('/proximity-analysis/location/{latitude}/{longitude}', [ProximityAnalysisController::class, 'getLocationProximity'])->name('proximity_analysis.location');
    Route::get('/proximity-analysis/walk-score/{latitude}/{longitude}', [ProximityAnalysisController::class, 'getWalkScore'])->name('proximity_analysis.walk_score');
    Route::get('/proximity-analysis/transit-score/{latitude}/{longitude}', [ProximityAnalysisController::class, 'getTransitScore'])->name('proximity_analysis.transit_score');
    Route::post('/proximity-analysis/batch', [ProximityAnalysisController::class, 'batchProximityAnalysis'])->name('proximity_analysis.batch');
    Route::post('/proximity-analysis/export', [ProximityAnalysisController::class, 'export'])->name('proximity_analysis.export');
    
    // Specific Proximity Analyses
    Route::get('/proximity-analysis/amenities', [ProximityAnalysisController::class, 'amenityProximityAnalysis'])->name('proximity_analysis.amenities');
    Route::get('/proximity-analysis/transportation', [ProximityAnalysisController::class, 'transportationProximityAnalysis'])->name('proximity_analysis.transportation');
    Route::get('/proximity-analysis/education', [ProximityAnalysisController::class, 'educationProximityAnalysis'])->name('proximity_analysis.education');
    Route::get('/proximity-analysis/healthcare', [ProximityAnalysisController::class, 'healthcareProximityAnalysis'])->name('proximity_analysis.healthcare');
    Route::get('/proximity-analysis/shopping', [ProximityAnalysisController::class, 'shoppingProximityAnalysis'])->name('proximity_analysis.shopping');
    Route::get('/proximity-analysis/recreation', [ProximityAnalysisController::class, 'recreationProximityAnalysis'])->name('proximity_analysis.recreation');

    // Demographic Analysis Routes
    Route::get('/demographic-analysis', [DemographicAnalysisController::class, 'index'])->name('demographic_analysis.index');
    Route::get('/demographic-analysis/create', [DemographicAnalysisController::class, 'create'])->name('demographic_analysis.create');
    Route::post('/demographic-analysis', [DemographicAnalysisController::class, 'store'])->name('demographic_analysis.store');
    Route::get('/demographic-analysis/{demographicAnalysis}', [DemographicAnalysisController::class, 'show'])->name('demographic_analysis.show');
    Route::get('/demographic-analysis/{demographicAnalysis}/edit', [DemographicAnalysisController::class, 'edit'])->name('demographic_analysis.edit');
    Route::put('/demographic-analysis/{demographicAnalysis}', [DemographicAnalysisController::class, 'update'])->name('demographic_analysis.update');
    Route::delete('/demographic-analysis/{demographicAnalysis}', [DemographicAnalysisController::class, 'destroy'])->name('demographic_analysis.destroy');
    
    // Demographic Analysis API Routes
    Route::get('/demographic-analysis/area/{latitude}/{longitude}', [DemographicAnalysisController::class, 'getAreaDemographics'])->name('demographic_analysis.area');
    Route::get('/demographic-analysis/trends/{latitude}/{longitude}', [DemographicAnalysisController::class, 'getDemographicTrends'])->name('demographic_analysis.trends');
    Route::get('/demographic-analysis/compare', [DemographicAnalysisController::class, 'compareDemographics'])->name('demographic_analysis.compare');
    Route::post('/demographic-analysis/export', [DemographicAnalysisController::class, 'export'])->name('demographic_analysis.export');
    
    // Specific Demographic Analyses
    Route::get('/demographic-analysis/population-density', [DemographicAnalysisController::class, 'populationDensityAnalysis'])->name('demographic_analysis.population_density');
    Route::get('/demographic-analysis/median-income', [DemographicAnalysisController::class, 'medianIncomeAnalysis'])->name('demographic_analysis.median_income');
    Route::get('/demographic-analysis/age-distribution', [DemographicAnalysisController::class, 'ageDistributionAnalysis'])->name('demographic_analysis.age_distribution');
    Route::get('/demographic-analysis/education-level', [DemographicAnalysisController::class, 'educationLevelAnalysis'])->name('demographic_analysis.education_level');
    Route::get('/demographic-analysis/employment-rate', [DemographicAnalysisController::class, 'employmentRateAnalysis'])->name('demographic_analysis.employment_rate');
    Route::get('/demographic-analysis/household-composition', [DemographicAnalysisController::class, 'householdCompositionAnalysis'])->name('demographic_analysis.household_composition');
    Route::get('/demographic-analysis/ethnic-diversity', [DemographicAnalysisController::class, 'ethnicDiversityAnalysis'])->name('demographic_analysis.ethnic_diversity');
    Route::get('/demographic-analysis/migration-patterns', [DemographicAnalysisController::class, 'migrationPatternsAnalysis'])->name('demographic_analysis.migration_patterns');

    // Property Density Routes
    Route::get('/property-density', [PropertyDensityController::class, 'index'])->name('property_density.index');
    Route::get('/property-density/create', [PropertyDensityController::class, 'create'])->name('property_density.create');
    Route::post('/property-density', [PropertyDensityController::class, 'store'])->name('property_density.store');
    Route::get('/property-density/{propertyDensity}', [PropertyDensityController::class, 'show'])->name('property_density.show');
    Route::get('/property-density/{propertyDensity}/edit', [PropertyDensityController::class, 'edit'])->name('property_density.edit');
    Route::put('/property-density/{propertyDensity}', [PropertyDensityController::class, 'update'])->name('property_density.update');
    Route::delete('/property-density/{propertyDensity}', [PropertyDensityController::class, 'destroy'])->name('property_density.destroy');
    
    // Property Density API Routes
    Route::get('/property-density/area/{latitude}/{longitude}', [PropertyDensityController::class, 'getAreaDensity'])->name('property_density.area');
    Route::get('/property-density/heatmap-data', [PropertyDensityController::class, 'getDensityHeatmapData'])->name('property_density.heatmap_data');
    Route::get('/property-density/trends/{latitude}/{longitude}', [PropertyDensityController::class, 'getDensityTrends'])->name('property_density.trends');
    Route::get('/property-density/compare', [PropertyDensityController::class, 'compareDensity'])->name('property_density.compare');
    Route::post('/property-density/export', [PropertyDensityController::class, 'export'])->name('property_density.export');

    // Walk Score Routes
    Route::get('/walk-score', [WalkScoreController::class, 'index'])->name('walk_score.index');
    Route::get('/walk-score/create', [WalkScoreController::class, 'create'])->name('walk_score.create');
    Route::post('/walk-score', [WalkScoreController::class, 'store'])->name('walk_score.store');
    Route::get('/walk-score/{walkScore}', [WalkScoreController::class, 'show'])->name('walk_score.show');
    Route::get('/walk-score/{walkScore}/edit', [WalkScoreController::class, 'edit'])->name('walk_score.edit');
    Route::put('/walk-score/{walkScore}', [WalkScoreController::class, 'update'])->name('walk_score.update');
    Route::delete('/walk-score/{walkScore}', [WalkScoreController::class, 'destroy'])->name('walk_score.destroy');
    
    // Walk Score API Routes
    Route::get('/walk-score/location/{latitude}/{longitude}', [WalkScoreController::class, 'getLocationWalkScore'])->name('walk_score.location');
    Route::get('/walk-score/heatmap-data', [WalkScoreController::class, 'getWalkScoreHeatmapData'])->name('walk_score.heatmap_data');
    Route::get('/walk-score/compare', [WalkScoreController::class, 'compareWalkScores'])->name('walk_score.compare');
    Route::post('/walk-score/export', [WalkScoreController::class, 'export'])->name('walk_score.export');

    // Transit Score Routes
    Route::get('/transit-score', [TransitScoreController::class, 'index'])->name('transit_score.index');
    Route::get('/transit-score/create', [TransitScoreController::class, 'create'])->name('transit_score.create');
    Route::post('/transit-score', [TransitScoreController::class, 'store'])->name('transit_score.store');
    Route::get('/transit-score/{transitScore}', [TransitScoreController::class, 'show'])->name('transit_score.show');
    Route::get('/transit-score/{transitScore}/edit', [TransitScoreController::class, 'edit'])->name('transit_score.edit');
    Route::put('/transit-score/{transitScore}', [TransitScoreController::class, 'update'])->name('transit_score.update');
    Route::delete('/transit-score/{transitScore}', [TransitScoreController::class, 'destroy'])->name('transit_score.destroy');
    
    // Transit Score API Routes
    Route::get('/transit-score/location/{latitude}/{longitude}', [TransitScoreController::class, 'getLocationTransitScore'])->name('transit_score.location');
    Route::get('/transit-score/heatmap-data', [TransitScoreController::class, 'getTransitScoreHeatmapData'])->name('transit_score.heatmap_data');
    Route::get('/transit-score/compare', [TransitScoreController::class, 'compareTransitScores'])->name('transit_score.compare');
    Route::post('/transit-score/export', [TransitScoreController::class, 'export'])->name('transit_score.export');

    // School District Routes
    Route::get('/school-districts', [SchoolDistrictController::class, 'index'])->name('school_districts.index');
    Route::get('/school-districts/create', [SchoolDistrictController::class, 'create'])->name('school_districts.create');
    Route::post('/school-districts', [SchoolDistrictController::class, 'store'])->name('school_districts.store');
    Route::get('/school-districts/{schoolDistrict}', [SchoolDistrictController::class, 'show'])->name('school_districts.show');
    Route::get('/school-districts/{schoolDistrict}/edit', [SchoolDistrictController::class, 'edit'])->name('school_districts.edit');
    Route::put('/school-districts/{schoolDistrict}', [SchoolDistrictController::class, 'update'])->name('school_districts.update');
    Route::delete('/school-districts/{schoolDistrict}', [SchoolDistrictController::class, 'destroy'])->name('school_districts.destroy');
    
    // School District API Routes
    Route::get('/school-districts/location/{latitude}/{longitude}', [SchoolDistrictController::class, 'getLocationSchoolDistricts'])->name('school_districts.location');
    Route::get('/school-districts/heatmap-data', [SchoolDistrictController::class, 'getSchoolDistrictHeatmapData'])->name('school_districts.heatmap_data');
    Route::get('/school-districts/compare', [SchoolDistrictController::class, 'compareSchoolDistricts'])->name('school_districts.compare');
    Route::post('/school-districts/export', [SchoolDistrictController::class, 'export'])->name('school_districts.export');

    // Crime Map Routes
    Route::get('/crime-maps', [CrimeMapController::class, 'index'])->name('crime_maps.index');
    Route::get('/crime-maps/create', [CrimeMapController::class, 'create'])->name('crime_maps.create');
    Route::post('/crime-maps', [CrimeMapController::class, 'store'])->name('crime_maps.store');
    Route::get('/crime-maps/{crimeMap}', [CrimeMapController::class, 'show'])->name('crime_maps.show');
    Route::get('/crime-maps/{crimeMap}/edit', [CrimeMapController::class, 'edit'])->name('crime_maps.edit');
    Route::put('/crime-maps/{crimeMap}', [CrimeMapController::class, 'update'])->name('crime_maps.update');
    Route::delete('/crime-maps/{crimeMap}', [CrimeMapController::class, 'destroy'])->name('crime_maps.destroy');
    
    // Crime Map API Routes
    Route::get('/crime-maps/location/{latitude}/{longitude}', [CrimeMapController::class, 'getLocationCrimeData'])->name('crime_maps.location');
    Route::get('/crime-maps/heatmap-data', [CrimeMapController::class, 'getCrimeHeatmapData'])->name('crime_maps.heatmap_data');
    Route::get('/crime-maps/trends/{latitude}/{longitude}', [CrimeMapController::class, 'getCrimeTrends'])->name('crime_maps.trends');
    Route::post('/crime-maps/export', [CrimeMapController::class, 'export'])->name('crime_maps.export');

    // Flood Risk Routes
    Route::get('/flood-risks', [FloodRiskController::class, 'index'])->name('flood_risks.index');
    Route::get('/flood-risks/create', [FloodRiskController::class, 'create'])->name('flood_risks.create');
    Route::post('/flood-risks', [FloodRiskController::class, 'store'])->name('flood_risks.store');
    Route::get('/flood-risks/{floodRisk}', [FloodRiskController::class, 'show'])->name('flood_risks.show');
    Route::get('/flood-risks/{floodRisk}/edit', [FloodRiskController::class, 'edit'])->name('flood_risks.edit');
    Route::put('/flood-risks/{floodRisk}', [FloodRiskController::class, 'update'])->name('flood_risks.update');
    Route::delete('/flood-risks/{floodRisk}', [FloodRiskController::class, 'destroy'])->name('flood_risks.destroy');
    
    // Flood Risk API Routes
    Route::get('/flood-risks/location/{latitude}/{longitude}', [FloodRiskController::class, 'getLocationFloodRisk'])->name('flood_risks.location');
    Route::get('/flood-risks/heatmap-data', [FloodRiskController::class, 'getFloodRiskHeatmapData'])->name('flood_risks.heatmap_data');
    Route::get('/flood-risks/projections/{latitude}/{longitude}', [FloodRiskController::class, 'getFloodRiskProjections'])->name('flood_risks.projections');
    Route::post('/flood-risks/export', [FloodRiskController::class, 'export'])->name('flood_risks.export');

    // Earthquake Risk Routes
    Route::get('/earthquake-risks', [EarthquakeRiskController::class, 'index'])->name('earthquake_risks.index');
    Route::get('/earthquake-risks/create', [EarthquakeRiskController::class, 'create'])->name('earthquake_risks.create');
    Route::post('/earthquake-risks', [EarthquakeRiskController::class, 'store'])->name('earthquake_risks.store');
    Route::get('/earthquake-risks/{earthquakeRisk}', [EarthquakeRiskController::class, 'show'])->name('earthquake_risks.show');
    Route::get('/earthquake-risks/{earthquakeRisk}/edit', [EarthquakeRiskController::class, 'edit'])->name('earthquake_risks.edit');
    Route::put('/earthquake-risks/{earthquakeRisk}', [EarthquakeRiskController::class, 'update'])->name('earthquake_risks.update');
    Route::delete('/earthquake-risks/{earthquakeRisk}', [EarthquakeRiskController::class, 'destroy'])->name('earthquake_risks.destroy');
    
    // Earthquake Risk API Routes
    Route::get('/earthquake-risks/location/{latitude}/{longitude}', [EarthquakeRiskController::class, 'getLocationEarthquakeRisk'])->name('earthquake_risks.location');
    Route::get('/earthquake-risks/heatmap-data', [EarthquakeRiskController::class, 'getEarthquakeRiskHeatmapData'])->name('earthquake_risks.heatmap_data');
    Route::get('/earthquake-risks/seismic-hazard/{latitude}/{longitude}', [EarthquakeRiskController::class, 'getSeismicHazardAnalysis'])->name('earthquake_risks.seismic_hazard');
    Route::post('/earthquake-risks/export', [EarthquakeRiskController::class, 'export'])->name('earthquake_risks.export');

    // Property Appreciation Map Routes
    Route::get('/property-appreciation-maps', [PropertyAppreciationMapController::class, 'index'])->name('property_appreciation_maps.index');
    Route::get('/property-appreciation-maps/create', [PropertyAppreciationMapController::class, 'create'])->name('property_appreciation_maps.create');
    Route::post('/property-appreciation-maps', [PropertyAppreciationMapController::class, 'store'])->name('property_appreciation_maps.store');
    Route::get('/property-appreciation-maps/{propertyAppreciationMap}', [PropertyAppreciationMapController::class, 'show'])->name('property_appreciation_maps.show');
    Route::get('/property-appreciation-maps/{propertyAppreciationMap}/edit', [PropertyAppreciationMapController::class, 'edit'])->name('property_appreciation_maps.edit');
    Route::put('/property-appreciation-maps/{propertyAppreciationMap}', [PropertyAppreciationMapController::class, 'update'])->name('property_appreciation_maps.update');
    Route::delete('/property-appreciation-maps/{propertyAppreciationMap}', [PropertyAppreciationMapController::class, 'destroy'])->name('property_appreciation_maps.destroy');
    
    // Property Appreciation Map API Routes
    Route::get('/property-appreciation-maps/location/{latitude}/{longitude}', [PropertyAppreciationMapController::class, 'getLocationAppreciation'])->name('property_appreciation_maps.location');
    Route::get('/property-appreciation-maps/heatmap-data', [PropertyAppreciationMapController::class, 'getAppreciationHeatmap'])->name('property_appreciation_maps.heatmap_data');
    Route::get('/property-appreciation-maps/trends/{latitude}/{longitude}', [PropertyAppreciationMapController::class, 'getAppreciationTrends'])->name('property_appreciation_maps.trends');
    Route::post('/property-appreciation-maps/export', [PropertyAppreciationMapController::class, 'export'])->name('property_appreciation_maps.export');

    // Risk Analysis Dashboard (Combined View)
    Route::get('/risk-analysis', function() {
        return redirect()->route('geospatial.flood_risks.index');
    })->name('risk_analysis.index');

    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        // General Analytics API
        Route::get('/analytics/stats', [GeospatialAnalyticsController::class, 'getAnalyticsStats'])->name('analytics.stats');
        Route::get('/analytics/quick-analysis', [GeospatialAnalyticsController::class, 'quickAnalysis'])->name('analytics.quick_analysis');
        
        // Heatmap API
        Route::get('/heatmaps/real-time', [HeatmapController::class, 'realTimeHeatmap'])->name('heatmaps.real_time');
        Route::post('/heatmaps/generate', [HeatmapController::class, 'generateHeatmap'])->name('heatmaps.generate');
        
        // Location Intelligence API
        Route::get('/location-intelligence/score', [LocationIntelligenceController::class, 'calculateLocationScore'])->name('location_intelligence.score');
        Route::get('/location-intelligence/recommendations', [LocationIntelligenceController::class, 'getRecommendations'])->name('location_intelligence.recommendations');
        
        // Risk Analysis API
        Route::get('/risk/assessment', function() {
            // Combined risk assessment endpoint
            return response()->json(['message' => 'Combined risk assessment endpoint']);
        })->name('risk.assessment');
    });
});
