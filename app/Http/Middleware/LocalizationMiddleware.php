<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get supported languages
        $supportedLanguages = $this->getSupportedLanguages();
        
        // Determine the locale to use
        $locale = $this->determineLocale($request, $supportedLanguages);
        
        // Set the application locale
        App::setLocale($locale);
        
        // Set locale in session for future requests
        Session::put('locale', $locale);
        
        // Set locale for Carbon (dates)
        if (class_exists('\Carbon\Carbon')) {
            \Carbon\Carbon::setLocale($locale);
        }
        
        // Add locale to request for easy access
        $request->merge(['locale' => $locale]);
        
        // Set RTL/LTR direction
        $direction = $this->getLanguageDirection($locale);
        $request->merge(['text_direction' => $direction]);
        
        // Share locale with all views
        view()->share('locale', $locale);
        view()->share('text_direction', $direction);
        view()->share('supported_languages', $supportedLanguages);
        
        return $next($request);
    }
    
    /**
     * Get supported languages
     *
     * @return array
     */
    private function getSupportedLanguages()
    {
        return [
            'ar' => [
                'name' => 'العربية',
                'native' => 'العربية',
                'code' => 'ar',
                'direction' => 'rtl',
                'flag' => '🇸🇦'
            ],
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'code' => 'en',
                'direction' => 'ltr',
                'flag' => '🇺🇸'
            ],
            'fr' => [
                'name' => 'Français',
                'native' => 'Français',
                'code' => 'fr',
                'direction' => 'ltr',
                'flag' => '🇫🇷'
            ],
            'es' => [
                'name' => 'Español',
                'native' => 'Español',
                'code' => 'es',
                'direction' => 'ltr',
                'flag' => '🇪🇸'
            ],
            'tr' => [
                'name' => 'Türkçe',
                'native' => 'Türkçe',
                'code' => 'tr',
                'direction' => 'ltr',
                'flag' => '🇹🇷'
            ]
        ];
    }
    
    /**
     * Determine the locale to use
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $supportedLanguages
     * @return string
     */
    private function determineLocale($request, $supportedLanguages)
    {
        // 1. Check if locale is explicitly set in request (query parameter or route)
        if ($request->has('locale') && isset($supportedLanguages[$request->get('locale')])) {
            return $request->get('locale');
        }
        
        // 2. Check session
        if (Session::has('locale') && isset($supportedLanguages[Session::get('locale')])) {
            return Session::get('locale');
        }
        
        // 3. Check user preference (if authenticated)
        if (auth()->check() && auth()->user()->language) {
            $userLanguage = auth()->user()->language;
            if (isset($supportedLanguages[$userLanguage])) {
                return $userLanguage;
            }
        }
        
        // 4. Check Accept-Language header
        $headerLocale = $this->getLocaleFromHeader($request, $supportedLanguages);
        if ($headerLocale) {
            return $headerLocale;
        }
        
        // 5. Check cookie
        if ($request->cookie('locale') && isset($supportedLanguages[$request->cookie('locale')])) {
            return $request->cookie('locale');
        }
        
        // 6. Default to application default or Arabic
        return config('app.locale', 'ar');
    }
    
    /**
     * Get locale from Accept-Language header
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $supportedLanguages
     * @return string|null
     */
    private function getLocaleFromHeader($request, $supportedLanguages)
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $part = trim($part);
            $parts2 = explode(';', $part);
            $lang = $parts2[0];
            $priority = 1.0;
            
            if (isset($parts2[1])) {
                $priorityParts = explode('=', $parts2[1]);
                if (isset($priorityParts[1])) {
                    $priority = (float) $priorityParts[1];
                }
            }
            
            $languages[$lang] = $priority;
        }
        
        // Sort by priority
        arsort($languages);
        
        // Find first supported language
        foreach (array_keys($languages) as $lang) {
            // Check exact match
            if (isset($supportedLanguages[$lang])) {
                return $lang;
            }
            
            // Check primary language (e.g., 'en' from 'en-US')
            $primaryLang = substr($lang, 0, 2);
            if (isset($supportedLanguages[$primaryLang])) {
                return $primaryLang;
            }
        }
        
        return null;
    }
    
    /**
     * Get text direction for language
     *
     * @param  string  $locale
     * @return string
     */
    private function getLanguageDirection($locale)
    {
        $rtlLanguages = ['ar', 'he', 'fa', 'ur', 'ku'];
        
        return in_array($locale, $rtlLanguages) ? 'rtl' : 'ltr';
    }
    
    /**
     * Change language dynamically
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function changeLanguage($request, $locale)
    {
        $supportedLanguages = (new self)->getSupportedLanguages();
        
        if (!isset($supportedLanguages[$locale])) {
            return redirect()->back()->with('error', 'اللغة غير مدعومة');
        }
        
        // Set in session
        Session::put('locale', $locale);
        
        // Set in cookie (longer persistence)
        cookie()->queue('locale', $locale, 525600); // 1 year
        
        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }
        
        return redirect()->back()->with('success', 'تم تغيير اللغة بنجاح');
    }
    
    /**
     * Get current language info
     *
     * @return array
     */
    public static function getCurrentLanguage()
    {
        $middleware = new self();
        $supportedLanguages = $middleware->getSupportedLanguages();
        $currentLocale = App::getLocale();
        
        return $supportedLanguages[$currentLocale] ?? $supportedLanguages['ar'];
    }
    
    /**
     * Get language switcher data
     *
     * @return array
     */
    public static function getLanguageSwitcherData()
    {
        $middleware = new self();
        $supportedLanguages = $middleware->getSupportedLanguages();
        $currentLocale = App::getLocale();
        
        $switcherData = [];
        
        foreach ($supportedLanguages as $code => $language) {
            $switcherData[] = [
                'code' => $code,
                'name' => $language['name'],
                'native' => $language['native'],
                'flag' => $language['flag'],
                'direction' => $language['direction'],
                'is_current' => $code === $currentLocale,
                'url' => request()->fullUrlWithQuery(['locale' => $code])
            ];
        }
        
        return $switcherData;
    }
}
