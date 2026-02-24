<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\LocalizationMiddleware;

class LocalizationController extends Controller
{
    /**
     * Switch the application language using LocalizationMiddleware
     */
    public function setLang($locale)
    {
        return LocalizationMiddleware::changeLanguage(request(), $locale);
    }

    /**
     * Get current language information
     */
    public function getCurrentLanguage()
    {
        $currentLang = LocalizationMiddleware::getCurrentLanguage();
        
        return response()->json([
            'locale' => app()->getLocale(),
            'name' => $currentLang['name'],
            'native' => $currentLang['native'],
            'direction' => $currentLang['direction'],
            'flag' => $currentLang['flag']
        ]);
    }

    /**
     * Get language switcher data for frontend
     */
    public function getLanguageSwitcherData()
    {
        $switcherData = LocalizationMiddleware::getLanguageSwitcherData();
        
        return response()->json($switcherData);
    }

    /**
     * Switch language via AJAX
     */
    public function switchLanguageAjax(Request $request)
    {
        $locale = $request->input('locale');
        
        try {
            $response = LocalizationMiddleware::changeLanguage($request, $locale);
            
            // If it's a redirect response, extract the success message
            if ($response->getSession()->has('success')) {
                return response()->json([
                    'success' => true,
                    'message' => $response->getSession()->get('success'),
                    'locale' => $locale,
                    'direction' => $locale === 'ar' ? 'rtl' : 'ltr'
                ]);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.language_change_error')
            ], 400);
        }
    }
}
