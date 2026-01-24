<?php

use App\Http\Controllers\Marketing\PropertyMarketingController;
use App\Http\Controllers\Marketing\ListingPromotionController;
use App\Http\Controllers\Marketing\EmailMarketingCampaignController;
use App\Http\Controllers\Marketing\SocialMediaMarketingController;
use App\Http\Controllers\Marketing\PropertyBrochureController;
use App\Http\Controllers\Marketing\VirtualOpenHouseMarketingController;
use App\Http\Controllers\Marketing\PropertyVideoMarketingController;
use App\Http\Controllers\Marketing\DroneFootageController;
use App\Http\Controllers\Marketing\PropertyInfluencerMarketingController;
use App\Http\Controllers\Marketing\PropertySeoController;
use App\Http\Controllers\Marketing\PropertyRetargetingController;
use Illuminate\Support\Facades\Route;

// Marketing Routes Group
Route::prefix('marketing')->name('marketing.')->middleware(['auth'])->group(function () {
    
    // Main Marketing Dashboard
    Route::get('/', [PropertyMarketingController::class, 'index'])->name('index');
    
    // Property Marketing Routes
    Route::prefix('property-marketing')->name('property-marketing.')->group(function () {
        Route::get('/', [PropertyMarketingController::class, 'index'])->name('index');
        Route::get('/create', [PropertyMarketingController::class, 'create'])->name('create');
        Route::post('/', [PropertyMarketingController::class, 'store'])->name('store');
        Route::get('/{propertyMarketing}', [PropertyMarketingController::class, 'show'])->name('show');
        Route::get('/{propertyMarketing}/edit', [PropertyMarketingController::class, 'edit'])->name('edit');
        Route::put('/{propertyMarketing}', [PropertyMarketingController::class, 'update'])->name('update');
        Route::delete('/{propertyMarketing}', [PropertyMarketingController::class, 'destroy'])->name('destroy');
        
        // Property Marketing Actions
        Route::post('/{propertyMarketing}/launch', [PropertyMarketingController::class, 'launch'])->name('launch');
        Route::post('/{propertyMarketing}/pause', [PropertyMarketingController::class, 'pause'])->name('pause');
        Route::post('/{propertyMarketing}/resume', [PropertyMarketingController::class, 'resume'])->name('resume');
        Route::post('/{propertyMarketing}/complete', [PropertyMarketingController::class, 'complete'])->name('complete');
        Route::get('/{propertyMarketing}/analytics', [PropertyMarketingController::class, 'analytics'])->name('analytics');
        Route::post('/{propertyMarketing}/duplicate', [PropertyMarketingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{propertyMarketing}/assets', [PropertyMarketingController::class, 'assets'])->name('assets');
        Route::post('/{propertyMarketing}/assets', [PropertyMarketingController::class, 'uploadAsset'])->name('upload-asset');
        Route::delete('/{propertyMarketing}/assets/{asset}', [PropertyMarketingController::class, 'deleteAsset'])->name('delete-asset');
        Route::get('/{propertyMarketing}/report', [PropertyMarketingController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertyMarketingController::class, 'export'])->name('export');
    });
    
    // Listing Promotion Routes
    Route::prefix('listing-promotions')->name('listing-promotions.')->group(function () {
        Route::get('/', [ListingPromotionController::class, 'index'])->name('index');
        Route::get('/create', [ListingPromotionController::class, 'create'])->name('create');
        Route::post('/', [ListingPromotionController::class, 'store'])->name('store');
        Route::get('/{listingPromotion}', [ListingPromotionController::class, 'show'])->name('show');
        Route::get('/{listingPromotion}/edit', [ListingPromotionController::class, 'edit'])->name('edit');
        Route::put('/{listingPromotion}', [ListingPromotionController::class, 'update'])->name('update');
        Route::delete('/{listingPromotion}', [ListingPromotionController::class, 'destroy'])->name('destroy');
        
        // Listing Promotion Actions
        Route::post('/{listingPromotion}/activate', [ListingPromotionController::class, 'activate'])->name('activate');
        Route::post('/{listingPromotion}/pause', [ListingPromotionController::class, 'pause'])->name('pause');
        Route::post('/{listingPromotion}/resume', [ListingPromotionController::class, 'resume'])->name('resume');
        Route::get('/{listingPromotion}/analytics', [ListingPromotionController::class, 'analytics'])->name('analytics');
        Route::post('/{listingPromotion}/duplicate', [ListingPromotionController::class, 'duplicate'])->name('duplicate');
        Route::get('/{listingPromotion}/assets', [ListingPromotionController::class, 'assets'])->name('assets');
        Route::post('/{listingPromotion}/assets', [ListingPromotionController::class, 'uploadAsset'])->name('upload-asset');
        Route::delete('/{listingPromotion}/assets/{asset}', [ListingPromotionController::class, 'deleteAsset'])->name('delete-asset');
        Route::get('/{listingPromotion}/report', [ListingPromotionController::class, 'generateReport'])->name('report');
        Route::get('/export', [ListingPromotionController::class, 'export'])->name('export');
    });
    
    // Email Marketing Campaign Routes
    Route::prefix('email-campaigns')->name('email-campaigns.')->group(function () {
        Route::get('/', [EmailMarketingCampaignController::class, 'index'])->name('index');
        Route::get('/create', [EmailMarketingCampaignController::class, 'create'])->name('create');
        Route::post('/', [EmailMarketingCampaignController::class, 'store'])->name('store');
        Route::get('/{emailCampaign}', [EmailMarketingCampaignController::class, 'show'])->name('show');
        Route::get('/{emailCampaign}/edit', [EmailMarketingCampaignController::class, 'edit'])->name('edit');
        Route::put('/{emailCampaign}', [EmailMarketingCampaignController::class, 'update'])->name('update');
        Route::delete('/{emailCampaign}', [EmailMarketingCampaignController::class, 'destroy'])->name('destroy');
        
        // Email Campaign Actions
        Route::post('/{emailCampaign}/send', [EmailMarketingCampaignController::class, 'send'])->name('send');
        Route::post('/{emailCampaign}/schedule', [EmailMarketingCampaignController::class, 'schedule'])->name('schedule');
        Route::post('/{emailCampaign}/cancel', [EmailMarketingCampaignController::class, 'cancel'])->name('cancel');
        Route::get('/{emailCampaign}/preview', [EmailMarketingCampaignController::class, 'preview'])->name('preview');
        Route::post('/{emailCampaign}/test', [EmailMarketingCampaignController::class, 'sendTest'])->name('test');
        Route::get('/{emailCampaign}/analytics', [EmailMarketingCampaignController::class, 'analytics'])->name('analytics');
        Route::post('/{emailCampaign}/duplicate', [EmailMarketingCampaignController::class, 'duplicate'])->name('duplicate');
        Route::get('/{emailCampaign}/assets', [EmailMarketingCampaignController::class, 'assets'])->name('assets');
        Route::post('/{emailCampaign}/assets', [EmailMarketingCampaignController::class, 'uploadAsset'])->name('upload-asset');
        Route::delete('/{emailCampaign}/assets/{asset}', [EmailMarketingCampaignController::class, 'deleteAsset'])->name('delete-asset');
        Route::get('/{emailCampaign}/report', [EmailMarketingCampaignController::class, 'generateReport'])->name('report');
        Route::get('/export', [EmailMarketingCampaignController::class, 'export'])->name('export');
    });
    
    // Social Media Marketing Routes
    Route::prefix('social-media')->name('social-media.')->group(function () {
        Route::get('/', [SocialMediaMarketingController::class, 'index'])->name('index');
        Route::get('/create', [SocialMediaMarketingController::class, 'create'])->name('create');
        Route::post('/', [SocialMediaMarketingController::class, 'store'])->name('store');
        Route::get('/{socialMediaPost}', [SocialMediaMarketingController::class, 'show'])->name('show');
        Route::get('/{socialMediaPost}/edit', [SocialMediaMarketingController::class, 'edit'])->name('edit');
        Route::put('/{socialMediaPost}', [SocialMediaMarketingController::class, 'update'])->name('update');
        Route::delete('/{socialMediaPost}', [SocialMediaMarketingController::class, 'destroy'])->name('destroy');
        
        // Social Media Actions
        Route::post('/{socialMediaPost}/publish', [SocialMediaMarketingController::class, 'publish'])->name('publish');
        Route::post('/{socialMediaPost}/schedule', [SocialMediaMarketingController::class, 'schedule'])->name('schedule');
        Route::post('/{socialMediaPost}/cancel', [SocialMediaMarketingController::class, 'cancel'])->name('cancel');
        Route::get('/{socialMediaPost}/analytics', [SocialMediaMarketingController::class, 'analytics'])->name('analytics');
        Route::post('/{socialMediaPost}/duplicate', [SocialMediaMarketingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{socialMediaPost}/media', [SocialMediaMarketingController::class, 'media'])->name('media');
        Route::post('/{socialMediaPost}/media', [SocialMediaMarketingController::class, 'uploadMedia'])->name('upload-media');
        Route::delete('/{socialMediaPost}/media/{media}', [SocialMediaMarketingController::class, 'deleteMedia'])->name('delete-media');
        Route::get('/{socialMediaPost}/report', [SocialMediaMarketingController::class, 'generateReport'])->name('report');
        Route::get('/export', [SocialMediaMarketingController::class, 'export'])->name('export');
    });
    
    // Property Brochure Routes
    Route::prefix('brochures')->name('brochures.')->group(function () {
        Route::get('/', [PropertyBrochureController::class, 'index'])->name('index');
        Route::get('/create', [PropertyBrochureController::class, 'create'])->name('create');
        Route::post('/', [PropertyBrochureController::class, 'store'])->name('store');
        Route::get('/{propertyBrochure}', [PropertyBrochureController::class, 'show'])->name('show');
        Route::get('/{propertyBrochure}/edit', [PropertyBrochureController::class, 'edit'])->name('edit');
        Route::put('/{propertyBrochure}', [PropertyBrochureController::class, 'update'])->name('update');
        Route::delete('/{propertyBrochure}', [PropertyBrochureController::class, 'destroy'])->name('destroy');
        
        // Brochure Actions
        Route::get('/{propertyBrochure}/generate-pdf', [PropertyBrochureController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/{propertyBrochure}/download', [PropertyBrochureController::class, 'download'])->name('download');
        Route::get('/{propertyBrochure}/preview', [PropertyBrochureController::class, 'preview'])->name('preview');
        Route::post('/{propertyBrochure}/duplicate', [PropertyBrochureController::class, 'duplicate'])->name('duplicate');
        Route::get('/{propertyBrochure}/analytics', [PropertyBrochureController::class, 'analytics'])->name('analytics');
        Route::get('/{propertyBrochure}/media', [PropertyBrochureController::class, 'media'])->name('media');
        Route::post('/{propertyBrochure}/media', [PropertyBrochureController::class, 'uploadMedia'])->name('upload-media');
        Route::delete('/{propertyBrochure}/media/{media}', [PropertyBrochureController::class, 'deleteMedia'])->name('delete-media');
        Route::get('/{propertyBrochure}/report', [PropertyBrochureController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertyBrochureController::class, 'export'])->name('export');
    });
    
    // Virtual Open House Marketing Routes
    Route::prefix('virtual-open-house')->name('virtual-open-house.')->group(function () {
        Route::get('/', [VirtualOpenHouseMarketingController::class, 'index'])->name('index');
        Route::get('/create', [VirtualOpenHouseMarketingController::class, 'create'])->name('create');
        Route::post('/', [VirtualOpenHouseMarketingController::class, 'store'])->name('store');
        Route::get('/{virtualOpenHouse}', [VirtualOpenHouseMarketingController::class, 'show'])->name('show');
        Route::get('/{virtualOpenHouse}/edit', [VirtualOpenHouseMarketingController::class, 'edit'])->name('edit');
        Route::put('/{virtualOpenHouse}', [VirtualOpenHouseMarketingController::class, 'update'])->name('update');
        Route::delete('/{virtualOpenHouse}', [VirtualOpenHouseMarketingController::class, 'destroy'])->name('destroy');
        
        // Virtual Open House Actions
        Route::post('/{virtualOpenHouse}/start', [VirtualOpenHouseMarketingController::class, 'start'])->name('start');
        Route::post('/{virtualOpenHouse}/end', [VirtualOpenHouseMarketingController::class, 'end'])->name('end');
        Route::post('/{virtualOpenHouse}/duplicate', [VirtualOpenHouseMarketingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{virtualOpenHouse}/analytics', [VirtualOpenHouseMarketingController::class, 'analytics'])->name('analytics');
        Route::get('/{virtualOpenHouse}/media', [VirtualOpenHouseMarketingController::class, 'media'])->name('media');
        Route::post('/{virtualOpenHouse}/media', [VirtualOpenHouseMarketingController::class, 'uploadMedia'])->name('upload-media');
        Route::delete('/{virtualOpenHouse}/media/{media}', [VirtualOpenHouseMarketingController::class, 'deleteMedia'])->name('delete-media');
        Route::get('/{virtualOpenHouse}/report', [VirtualOpenHouseMarketingController::class, 'generateReport'])->name('report');
        Route::get('/export', [VirtualOpenHouseMarketingController::class, 'export'])->name('export');
    });
    
    // Property Video Marketing Routes
    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [PropertyVideoMarketingController::class, 'index'])->name('index');
        Route::get('/create', [PropertyVideoMarketingController::class, 'create'])->name('create');
        Route::post('/', [PropertyVideoMarketingController::class, 'store'])->name('store');
        Route::get('/{propertyVideo}', [PropertyVideoMarketingController::class, 'show'])->name('show');
        Route::get('/{propertyVideo}/edit', [PropertyVideoMarketingController::class, 'edit'])->name('edit');
        Route::put('/{propertyVideo}', [PropertyVideoMarketingController::class, 'update'])->name('update');
        Route::delete('/{propertyVideo}', [PropertyVideoMarketingController::class, 'destroy'])->name('destroy');
        
        // Video Actions
        Route::post('/{propertyVideo}/publish', [PropertyVideoMarketingController::class, 'publish'])->name('publish');
        Route::post('/{propertyVideo}/duplicate', [PropertyVideoMarketingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{propertyVideo}/analytics', [PropertyVideoMarketingController::class, 'analytics'])->name('analytics');
        Route::get('/{propertyVideo}/media', [PropertyVideoMarketingController::class, 'media'])->name('media');
        Route::post('/{propertyVideo}/media', [PropertyVideoMarketingController::class, 'uploadMedia'])->name('upload-media');
        Route::delete('/{propertyVideo}/media/{media}', [PropertyVideoMarketingController::class, 'deleteMedia'])->name('delete-media');
        Route::get('/{propertyVideo}/report', [PropertyVideoMarketingController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertyVideoMarketingController::class, 'export'])->name('export');
    });
    
    // Drone Footage Routes
    Route::prefix('drone-footage')->name('drone-footage.')->group(function () {
        Route::get('/', [DroneFootageController::class, 'index'])->name('index');
        Route::get('/create', [DroneFootageController::class, 'create'])->name('create');
        Route::post('/', [DroneFootageController::class, 'store'])->name('store');
        Route::get('/{droneFootage}', [DroneFootageController::class, 'show'])->name('show');
        Route::get('/{droneFootage}/edit', [DroneFootageController::class, 'edit'])->name('edit');
        Route::put('/{droneFootage}', [DroneFootageController::class, 'update'])->name('update');
        Route::delete('/{droneFootage}', [DroneFootageController::class, 'destroy'])->name('destroy');
        
        // Drone Footage Actions
        Route::post('/{droneFootage}/publish', [DroneFootageController::class, 'publish'])->name('publish');
        Route::post('/{droneFootage}/duplicate', [DroneFootageController::class, 'duplicate'])->name('duplicate');
        Route::get('/{droneFootage}/analytics', [DroneFootageController::class, 'analytics'])->name('analytics');
        Route::get('/{droneFootage}/media', [DroneFootageController::class, 'media'])->name('media');
        Route::post('/{droneFootage}/media', [DroneFootageController::class, 'uploadMedia'])->name('upload-media');
        Route::delete('/{droneFootage}/media/{media}', [DroneFootageController::class, 'deleteMedia'])->name('delete-media');
        Route::get('/{droneFootage}/report', [DroneFootageController::class, 'generateReport'])->name('report');
        Route::get('/export', [DroneFootageController::class, 'export'])->name('export');
    });
    
    // Influencer Marketing Routes
    Route::prefix('influencer-marketing')->name('influencer-marketing.')->group(function () {
        Route::get('/', [PropertyInfluencerMarketingController::class, 'index'])->name('index');
        Route::get('/create', [PropertyInfluencerMarketingController::class, 'create'])->name('create');
        Route::post('/', [PropertyInfluencerMarketingController::class, 'store'])->name('store');
        Route::get('/{influencerCampaign}', [PropertyInfluencerMarketingController::class, 'show'])->name('show');
        Route::get('/{influencerCampaign}/edit', [PropertyInfluencerMarketingController::class, 'edit'])->name('edit');
        Route::put('/{influencerCampaign}', [PropertyInfluencerMarketingController::class, 'update'])->name('update');
        Route::delete('/{influencerCampaign}', [PropertyInfluencerMarketingController::class, 'destroy'])->name('destroy');
        
        // Influencer Campaign Actions
        Route::post('/{influencerCampaign}/launch', [PropertyInfluencerMarketingController::class, 'launch'])->name('launch');
        Route::post('/{influencerCampaign}/pause', [PropertyInfluencerMarketingController::class, 'pause'])->name('pause');
        Route::post('/{influencerCampaign}/resume', [PropertyInfluencerMarketingController::class, 'resume'])->name('resume');
        Route::post('/{influencerCampaign}/complete', [PropertyInfluencerMarketingController::class, 'complete'])->name('complete');
        Route::post('/{influencerCampaign}/duplicate', [PropertyInfluencerMarketingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{influencerCampaign}/analytics', [PropertyInfluencerMarketingController::class, 'analytics'])->name('analytics');
        Route::get('/{influencerCampaign}/assets', [PropertyInfluencerMarketingController::class, 'assets'])->name('assets');
        Route::post('/{influencerCampaign}/assets', [PropertyInfluencerMarketingController::class, 'uploadAsset'])->name('upload-asset');
        Route::delete('/{influencerCampaign}/assets/{asset}', [PropertyInfluencerMarketingController::class, 'deleteAsset'])->name('delete-asset');
        Route::get('/{influencerCampaign}/report', [PropertyInfluencerMarketingController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertyInfluencerMarketingController::class, 'export'])->name('export');
    });
    
    // Property SEO Routes
    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [PropertySeoController::class, 'index'])->name('index');
        Route::get('/create', [PropertySeoController::class, 'create'])->name('create');
        Route::post('/', [PropertySeoController::class, 'store'])->name('store');
        Route::get('/{propertySeo}', [PropertySeoController::class, 'show'])->name('show');
        Route::get('/{propertySeo}/edit', [PropertySeoController::class, 'edit'])->name('edit');
        Route::put('/{propertySeo}', [PropertySeoController::class, 'update'])->name('update');
        Route::delete('/{propertySeo}', [PropertySeoController::class, 'destroy'])->name('destroy');
        
        // SEO Actions
        Route::post('/{propertySeo}/audit', [PropertySeoController::class, 'runAudit'])->name('audit');
        Route::get('/{propertySeo}/analytics', [PropertySeoController::class, 'analytics'])->name('analytics');
        Route::get('/{propertySeo}/recommendations', [PropertySeoController::class, 'recommendations'])->name('recommendations');
        Route::post('/{propertySeo}/generate-og-image', [PropertySeoController::class, 'generateOgImage'])->name('generate-og-image');
        Route::post('/{propertySeo}/generate-twitter-image', [PropertySeoController::class, 'generateTwitterImage'])->name('generate-twitter-image');
        Route::get('/{propertySeo}/report', [PropertySeoController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertySeoController::class, 'export'])->name('export');
    });
    
    // Retargeting Routes
    Route::prefix('retargeting')->name('retargeting.')->group(function () {
        Route::get('/', [PropertyRetargetingController::class, 'index'])->name('index');
        Route::get('/create', [PropertyRetargetingController::class, 'create'])->name('create');
        Route::post('/', [PropertyRetargetingController::class, 'store'])->name('store');
        Route::get('/{retargetingAudience}', [PropertyRetargetingController::class, 'show'])->name('show');
        Route::get('/{retargetingAudience}/edit', [PropertyRetargetingController::class, 'edit'])->name('edit');
        Route::put('/{retargetingAudience}', [PropertyRetargetingController::class, 'update'])->name('update');
        Route::delete('/{retargetingAudience}', [PropertyRetargetingController::class, 'destroy'])->name('destroy');
        
        // Retargeting Actions
        Route::post('/{retargetingAudience}/activate', [PropertyRetargetingController::class, 'activate'])->name('activate');
        Route::post('/{retargetingAudience}/pause', [PropertyRetargetingController::class, 'pause'])->name('pause');
        Route::post('/{retargetingAudience}/resume', [PropertyRetargetingController::class, 'resume'])->name('resume');
        Route::post('/{retargetingAudience}/duplicate', [PropertyRetargetingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{retargetingAudience}/analytics', [PropertyRetargetingController::class, 'analytics'])->name('analytics');
        Route::get('/{retargetingAudience}/creative-assets', [PropertyRetargetingController::class, 'creativeAssets'])->name('creative-assets');
        Route::post('/{retargetingAudience}/creative-assets', [PropertyRetargetingController::class, 'uploadCreativeAsset'])->name('upload-creative-asset');
        Route::delete('/{retargetingAudience}/creative-assets/{asset}', [PropertyRetargetingController::class, 'deleteCreativeAsset'])->name('delete-creative-asset');
        Route::get('/{retargetingAudience}/report', [PropertyRetargetingController::class, 'generateReport'])->name('report');
        Route::get('/export', [PropertyRetargetingController::class, 'export'])->name('export');
    });
    
    // Marketing Analytics Dashboard
    Route::get('/analytics', [PropertyMarketingController::class, 'marketingAnalytics'])->name('analytics');
    
    // Marketing Reports
    Route::get('/reports', [PropertyMarketingController::class, 'marketingReports'])->name('reports');
    Route::post('/reports/generate', [PropertyMarketingController::class, 'generateMarketingReport'])->name('reports.generate');
    
    // Marketing Settings
    Route::get('/settings', [PropertyMarketingController::class, 'marketingSettings'])->name('settings');
    Route::post('/settings', [PropertyMarketingController::class, 'updateMarketingSettings'])->name('settings.update');
    
    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/stats', [PropertyMarketingController::class, 'getMarketingStats'])->name('stats');
        Route::get('/campaign-performance', [PropertyMarketingController::class, 'getCampaignPerformance'])->name('campaign-performance');
        Route::get('/channel-performance', [PropertyMarketingController::class, 'getChannelPerformance'])->name('channel-performance');
        Route::get('/roi-metrics', [PropertyMarketingController::class, 'getRoiMetrics'])->name('roi-metrics');
        Route::get('/lead-conversion', [PropertyMarketingController::class, 'getLeadConversion'])->name('lead-conversion');
        Route::get('/budget-utilization', [PropertyMarketingController::class, 'getBudgetUtilization'])->name('budget-utilization');
        Route::get('/audience-insights', [PropertyMarketingController::class, 'getAudienceInsights'])->name('audience-insights');
        Route::get('/competitor-analysis', [PropertyMarketingController::class, 'getCompetitorAnalysis'])->name('competitor-analysis');
        Route::get('/market-trends', [PropertyMarketingController::class, 'getMarketTrends'])->name('market-trends');
    });
});
