<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MultiLanguageService
{
    private const SUPPORTED_LANGUAGES = [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'direction' => 'ltr',
            'locale' => 'en_US',
            'flag' => '🇺🇸',
            'is_default' => true,
            'is_rtl' => false
        ],
        'ar' => [
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'code' => 'ar',
            'direction' => 'rtl',
            'locale' => 'ar_SA',
            'flag' => '🇸🇦',
            'is_default' => false,
            'is_rtl' => true
        ],
        'es' => [
            'name' => 'Spanish',
            'native_name' => 'Español',
            'code' => 'es',
            'direction' => 'ltr',
            'locale' => 'es_ES',
            'flag' => '🇪🇸',
            'is_default' => false,
            'is_rtl' => false
        ],
        'fr' => [
            'name' => 'French',
            'native_name' => 'Français',
            'code' => 'fr',
            'direction' => 'ltr',
            'locale' => 'fr_FR',
            'flag' => '🇫🇷',
            'is_default' => false,
            'is_rtl' => false
        ],
        'de' => [
            'name' => 'German',
            'native_name' => 'Deutsch',
            'code' => 'de',
            'direction' => 'ltr',
            'locale' => 'de_DE',
            'flag' => '🇩🇪',
            'is_default' => false,
            'is_rtl' => false
        ],
        'it' => [
            'name' => 'Italian',
            'native_name' => 'Italiano',
            'code' => 'it',
            'direction' => 'ltr',
            'locale' => 'it_IT',
            'flag' => '🇮🇹',
            'is_default' => false,
            'is_rtl' => false
        ],
        'pt' => [
            'name' => 'Portuguese',
            'native_name' => 'Português',
            'code' => 'pt',
            'direction' => 'ltr',
            'locale' => 'pt_BR',
            'flag' => '🇧🇷',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ru' => [
            'name' => 'Russian',
            'native_name' => 'Русский',
            'code' => 'ru',
            'direction' => 'ltr',
            'locale' => 'ru_RU',
            'flag' => '🇷🇺',
            'is_default' => false,
            'is_rtl' => false
        ],
        'zh' => [
            'name' => 'Chinese',
            'native_name' => '中文',
            'code' => 'zh',
            'direction' => 'ltr',
            'locale' => 'zh_CN',
            'flag' => '🇨🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ja' => [
            'name' => 'Japanese',
            'native_name' => '日本語',
            'code' => 'ja',
            'direction' => 'ltr',
            'locale' => 'ja_JP',
            'flag' => '🇯🇵',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ko' => [
            'name' => 'Korean',
            'native_name' => '한국어',
            'code' => 'ko',
            'direction' => 'ltr',
            'locale' => 'ko_KR',
            'flag' => '🇰🇷',
            'is_default' => false,
            'is_rtl' => false
        ],
        'hi' => [
            'name' => 'Hindi',
            'native_name' => 'हिन्दी',
            'code' => 'hi',
            'direction' => 'ltr',
            'locale' => 'hi_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'tr' => [
            'name' => 'Turkish',
            'native_name' => 'Türkçe',
            'code' => 'tr',
            'direction' => 'ltr',
            'locale' => 'tr_TR',
            'flag' => '🇹🇷',
            'is_default' => false,
            'is_rtl' => false
        ],
        'pl' => [
            'name' => 'Polish',
            'native_name' => 'Polski',
            'code' => 'pl',
            'direction' => 'ltr',
            'locale' => 'pl_PL',
            'flag' => '🇵🇱',
            'is_default' => false,
            'is_rtl' => false
        ],
        'nl' => [
            'name' => 'Dutch',
            'native_name' => 'Nederlands',
            'code' => 'nl',
            'direction' => 'ltr',
            'locale' => 'nl_NL',
            'flag' => '🇳🇱',
            'is_default' => false,
            'is_rtl' => false
        ],
        'sv' => [
            'name' => 'Swedish',
            'native_name' => 'Svenska',
            'code' => 'sv',
            'direction' => 'ltr',
            'locale' => 'sv_SE',
            'flag' => '🇸🇪',
            'is_default' => false,
            'is_rtl' => false
        ],
        'no' => [
            'name' => 'Norwegian',
            'native_name' => 'Norsk',
            'code' => 'no',
            'direction' => 'ltr',
            'locale' => 'no_NO',
            'flag' => '🇳🇴',
            'is_default' => false,
            'is_rtl' => false
        ],
        'da' => [
            'name' => 'Danish',
            'native_name' => 'Dansk',
            'code' => 'da',
            'direction' => 'ltr',
            'locale' => 'da_DK',
            'flag' => '🇩🇰',
            'is_default' => false,
            'is_rtl' => false
        ],
        'fi' => [
            'name' => 'Finnish',
            'native_name' => 'Suomi',
            'code' => 'fi',
            'direction' => 'ltr',
            'locale' => 'fi_FI',
            'flag' => '🇫🇮',
            'is_default' => false,
            'is_rtl' => false
        ],
        'el' => [
            'name' => 'Greek',
            'native_name' => 'Ελληνικά',
            'code' => 'el',
            'direction' => 'ltr',
            'locale' => 'el_GR',
            'flag' => '🇬🇷',
            'is_default' => false,
            'is_rtl' => false
        ],
        'he' => [
            'name' => 'Hebrew',
            'native_name' => 'עברית',
            'code' => 'he',
            'direction' => 'rtl',
            'locale' => 'he_IL',
            'flag' => '🇮🇱',
            'is_default' => false,
            'is_rtl' => true
        ],
        'th' => [
            'name' => 'Thai',
            'native_name' => 'ไทย',
            'code' => 'th',
            'direction' => 'ltr',
            'locale' => 'th_TH',
            'flag' => '🇹🇭',
            'is_default' => false,
            'is_rtl' => false
        ],
        'vi' => [
            'name' => 'Vietnamese',
            'native_name' => 'Tiếng Việt',
            'code' => 'vi',
            'direction' => 'ltr',
            'locale' => 'vi_VN',
            'flag' => '🇻🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'id' => [
            'name' => 'Indonesian',
            'native_name' => 'Bahasa Indonesia',
            'code' => 'id',
            'direction' => 'ltr',
            'locale' => 'id_ID',
            'flag' => '🇮🇩',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ms' => [
            'name' => 'Malay',
            'native_name' => 'Bahasa Melayu',
            'code' => 'ms',
            'direction' => 'ltr',
            'locale' => 'ms_MY',
            'flag' => '🇲🇾',
            'is_default' => false,
            'is_rtl' => false
        ],
        'tl' => [
            'name' => 'Filipino',
            'native_name' => 'Filipino',
            'code' => 'tl',
            'direction' => 'ltr',
            'locale' => 'tl_PH',
            'flag' => '🇵🇭',
            'is_default' => false,
            'is_rtl' => false
        ],
        'sw' => [
            'name' => 'Swahili',
            'native_name' => 'Kiswahili',
            'code' => 'sw',
            'direction' => 'ltr',
            'locale' => 'sw_KE',
            'flag' => '🇰🇪',
            'is_default' => false,
            'is_rtl' => false
        ],
        'af' => [
            'name' => 'Afrikaans',
            'native_name' => 'Afrikaans',
            'code' => 'af',
            'direction' => 'ltr',
            'locale' => 'af_ZA',
            'flag' => '🇿🇦',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ur' => [
            'name' => 'Urdu',
            'native_name' => 'اردو',
            'code' => 'ur',
            'direction' => 'rtl',
            'locale' => 'ur_PK',
            'flag' => '🇵🇰',
            'is_default' => false,
            'is_rtl' => true
        ],
        'bn' => [
            'name' => 'Bengali',
            'native_name' => 'বাংলা',
            'code' => 'bn',
            'direction' => 'ltr',
            'locale' => 'bn_BD',
            'flag' => '🇧🇩',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ta' => [
            'name' => 'Tamil',
            'native_name' => 'தமிழ்',
            'code' => 'ta',
            'direction' => 'ltr',
            'locale' => 'ta_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'te' => [
            'name' => 'Telugu',
            'native_name' => 'తెలుగు',
            'code' => 'te',
            'direction' => 'ltr',
            'locale' => 'te_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'mr' => [
            'name' => 'Marathi',
            'native_name' => 'मराठी',
            'code' => 'mr',
            'direction' => 'ltr',
            'locale' => 'mr_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'gu' => [
            'name' => 'Gujarati',
            'native_name' => 'ગુજરાતી',
            'code' => 'gu',
            'direction' => 'ltr',
            'locale' => 'gu_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'kn' => [
            'name' => 'Kannada',
            'native_name' => 'ಕನ್ನಡ',
            'code' => 'kn',
            'direction' => 'ltr',
            'locale' => 'kn_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'ml' => [
            'name' => 'Malayalam',
            'native_name' => 'മലയാളം',
            'code' => 'ml',
            'direction' => 'ltr',
            'locale' => 'ml_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'pa' => [
            'name' => 'Punjabi',
            'native_name' => 'ਪੰਜਾਬੀ',
            'code' => 'pa',
            'direction' => 'ltr',
            'locale' => 'pa_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'or' => [
            'name' => 'Odia',
            'native_name' => 'ଓଡ଼ିଆ',
            'code' => 'or',
            'direction' => 'ltr',
            'locale' => 'or_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ],
        'as' => [
            'name' => 'Assamese',
            'native_name' => 'অসমীয়া',
            'code' => 'as',
            'direction' => 'ltr',
            'locale' => 'as_IN',
            'flag' => '🇮🇳',
            'is_default' => false,
            'is_rtl' => false
        ]
    ];

    private const CACHE_DURATION = 3600; // 1 hour

    public function translate(string $key, string $language = null, array $params = []): string
    {
        try {
            $language = $language ?? $this->getCurrentLanguage();
            
            // Check cache first
            $cacheKey = "translation_{$language}_{$key}";
            $cachedTranslation = Cache::get($cacheKey);
            
            if ($cachedTranslation !== null) {
                return $this->replaceParameters($cachedTranslation, $params);
            }

            // Get translation from database
            $translation = Translation::where('key', $key)
                ->where('language', $language)
                ->value('value');

            if ($translation) {
                Cache::put($cacheKey, $translation, self::CACHE_DURATION);
                return $this->replaceParameters($translation, $params);
            }

            // Fallback to default language
            $defaultLanguage = config('app.fallback_locale', 'en');
            if ($language !== $defaultLanguage) {
                $fallbackTranslation = $this->translate($key, $defaultLanguage, $params);
                Cache::put($cacheKey, $fallbackTranslation, self::CACHE_DURATION);
                return $fallbackTranslation;
            }

            // Return key if no translation found
            return $key;
        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'error' => $e->getMessage(),
                'key' => $key,
                'language' => $language
            ]);
            return $key;
        }
    }

    public function addTranslation(string $key, string $value, string $language): array
    {
        try {
            if (!$this->isLanguageSupported($language)) {
                return [
                    'success' => false,
                    'message' => 'Language not supported'
                ];
            }

            $translation = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'language' => $language
                ],
                [
                    'value' => $value,
                    'updated_at' => now()
                ]
            );

            // Clear cache
            $this->clearTranslationCache($key, $language);

            return [
                'success' => true,
                'translation' => $translation,
                'message' => 'Translation added successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to add translation', [
                'error' => $e->getMessage(),
                'key' => $key,
                'language' => $language
            ]);

            return [
                'success' => false,
                'message' => 'Failed to add translation',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getLanguageInfo(string $languageCode): ?array
    {
        return self::SUPPORTED_LANGUAGES[$languageCode] ?? null;
    }

    public function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }

    public function getCurrentLanguage(): string
    {
        // Try to get from session
        if (session()->has('locale')) {
            return session('locale');
        }

        // Try to get from user preference
        if (auth()->check()) {
            $userLanguage = auth()->user()->preferred_language;
            if ($userLanguage && $this->isLanguageSupported($userLanguage)) {
                return $userLanguage;
            }
        }

        // Try to detect from browser
        $browserLanguage = $this->detectBrowserLanguage();
        if ($browserLanguage && $this->isLanguageSupported($browserLanguage)) {
            return $browserLanguage;
        }

        // Fall back to default
        return config('app.locale', 'en');
    }

    public function setLanguage(string $languageCode): array
    {
        try {
            if (!$this->isLanguageSupported($languageCode)) {
                return [
                    'success' => false,
                    'message' => 'Language not supported'
                ];
            }

            // Set in session
            session(['locale' => $languageCode]);

            // Update user preference if authenticated
            if (auth()->check()) {
                auth()->user()->update(['preferred_language' => $languageCode]);
            }

            // Set locale for current request
            app()->setLocale($languageCode);

            return [
                'success' => true,
                'language' => $languageCode,
                'language_info' => $this->getLanguageInfo($languageCode),
                'message' => 'Language set successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to set language', [
                'error' => $e->getMessage(),
                'language' => $languageCode
            ]);

            return [
                'success' => false,
                'message' => 'Failed to set language',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTranslations(string $language, array $filters = []): array
    {
        try {
            $query = Translation::where('language', $language);

            // Apply filters
            if (isset($filters['group'])) {
                $query->where('group', $filters['group']);
            }

            if (isset($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('key', 'LIKE', "%{$filters['search']}%")
                      ->orWhere('value', 'LIKE', "%{$filters['search']}%");
                });
            }

            $translations = $query->orderBy('key')->get();

            return [
                'success' => true,
                'translations' => $translations->map(function($translation) {
                    return [
                        'key' => $translation->key,
                        'value' => $translation->value,
                        'group' => $translation->group,
                        'updated_at' => $translation->updated_at->toISOString()
                    ];
                }),
                'total_count' => $translations->count()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get translations', [
                'error' => $e->getMessage(),
                'language' => $language
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get translations',
                'error' => $e->getMessage()
            ];
        }
    }

    public function importTranslations(array $translations, string $language): array
    {
        try {
            if (!$this->isLanguageSupported($language)) {
                return [
                    'success' => false,
                    'message' => 'Language not supported'
                ];
            }

            $importedCount = 0;
            $errorCount = 0;

            foreach ($translations as $key => $value) {
                try {
                    $this->addTranslation($key, $value, $language);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Failed to import translation: {$key}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Clear all caches for this language
            $this->clearLanguageCache($language);

            return [
                'success' => true,
                'imported_count' => $importedCount,
                'error_count' => $errorCount,
                'total_count' => count($translations),
                'language' => $language,
                'message' => 'Translations imported successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to import translations', [
                'error' => $e->getMessage(),
                'language' => $language
            ]);

            return [
                'success' => false,
                'message' => 'Failed to import translations',
                'error' => $e->getMessage()
            ];
        }
    }

    public function exportTranslations(string $language): array
    {
        try {
            $translations = Translation::where('language', $language)
                ->orderBy('key')
                ->get();

            $exportData = [];
            foreach ($translations as $translation) {
                $exportData[$translation->key] = $translation->value;
            }

            return [
                'success' => true,
                'language' => $language,
                'translations' => $exportData,
                'exported_at' => now()->toISOString(),
                'total_count' => count($exportData)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to export translations', [
                'error' => $e->getMessage(),
                'language' => $language
            ]);

            return [
                'success' => false,
                'message' => 'Failed to export translations',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getLanguageStatistics(): array
    {
        try {
            $stats = [];
            
            foreach (self::SUPPORTED_LANGUAGES as $code => $info) {
                $translationCount = Translation::where('language', $code)->count();
                $userCount = User::where('preferred_language', $code)->count();
                
                $stats[$code] = [
                    'name' => $info['name'],
                    'native_name' => $info['native_name'],
                    'flag' => $info['flag'],
                    'direction' => $info['direction'],
                    'translation_count' => $translationCount,
                    'user_count' => $userCount,
                    'completion_percentage' => $this->calculateCompletionPercentage($code),
                    'is_rtl' => $info['is_rtl'],
                    'is_default' => $info['is_default']
                ];
            }

            // Sort by user count descending
            uasort($stats, function($a, $b) {
                return $b['user_count'] - $a['user_count'];
            });

            return [
                'success' => true,
                'statistics' => $stats,
                'total_languages' => count(self::SUPPORTED_LANGUAGES),
                'total_translations' => Translation::count(),
                'total_users' => User::count(),
                'most_popular' => array_keys($stats, max(array_column($stats, 'user_count')))[0],
                'least_popular' => array_keys($stats, min(array_column($stats, 'user_count')))[0]
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get language statistics', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateLanguageFiles(): array
    {
        try {
            $generatedFiles = [];
            $errors = [];

            foreach (self::SUPPORTED_LANGUAGES as $code => $info) {
                try {
                    $translations = Translation::where('language', $code)
                        ->orderBy('key')
                        ->get();

                    $languageData = [];
                    foreach ($translations as $translation) {
                        // Handle nested keys (e.g., "menu.home.title")
                        $this->setNestedValue($languageData, $translation->key, $translation->value);
                    }

                    // Generate JSON file
                    $jsonPath = resource_path("lang/{$code}.json");
                    file_put_contents($jsonPath, json_encode($languageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $generatedFiles[] = $jsonPath;

                    // Generate PHP file
                    $phpPath = resource_path("lang/{$code}/messages.php");
                    $phpContent = "<?php\n\nreturn " . var_export($languageData, true) . ";\n";
                    $this->ensureDirectoryExists(dirname($phpPath));
                    file_put_contents($phpPath, $phpContent);
                    $generatedFiles[] = $phpPath;

                } catch (\Exception $e) {
                    $errors[] = [
                        'language' => $code,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Failed to generate language files for {$code}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'success' => empty($errors),
                'generated_files' => $generatedFiles,
                'errors' => $errors,
                'total_files' => count($generatedFiles),
                'generated_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate language files', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to generate language files',
                'error' => $e->getMessage()
            ];
        }
    }

    public function detectBrowserLanguage(): ?string
    {
        $acceptLanguage = request()->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $part = trim($part);
            $segments = explode(';q=', $part);
            $lang = $segments[0];
            $quality = isset($segments[1]) ? (float) $segments[1] : 1.0;
            
            // Convert to our language code format
            $langCode = $this->normalizeLanguageCode($lang);
            
            if ($langCode && $this->isLanguageSupported($langCode)) {
                $languages[$langCode] = $quality;
            }
        }

        // Sort by quality
        arsort($languages);
        
        return array_key_first($languages);
    }

    public function isLanguageSupported(string $languageCode): bool
    {
        return isset(self::SUPPORTED_LANGUAGES[$languageCode]);
    }

    public function isRTL(string $languageCode): bool
    {
        $languageInfo = $this->getLanguageInfo($languageCode);
        return $languageInfo ? $languageInfo['is_rtl'] : false;
    }

    public function getLanguageDirection(string $languageCode): string
    {
        $languageInfo = $this->getLanguageInfo($languageCode);
        return $languageInfo ? $languageInfo['direction'] : 'ltr';
    }

    // Private helper methods
    private function replaceParameters(string $translation, array $params): string
    {
        foreach ($params as $key => $value) {
            $translation = str_replace(':' . $key, $value, $translation);
        }
        
        return $translation;
    }

    private function clearTranslationCache(string $key, string $language): void
    {
        $cacheKey = "translation_{$language}_{$key}";
        Cache::forget($cacheKey);
    }

    private function clearLanguageCache(string $language): void
    {
        $cacheKeys = Cache::getRedis()->keys("translation_{$language}_*");
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    private function calculateCompletionPercentage(string $language): float
    {
        $totalKeys = Translation::where('language', 'en')->distinct('key')->count('key');
        $translatedKeys = Translation::where('language', $language)->distinct('key')->count('key');
        
        return $totalKeys > 0 ? ($translatedKeys / $totalKeys) * 100 : 0;
    }

    private function setNestedValue(array &$array, string $key, string $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    private function normalizeLanguageCode(string $code): ?string
    {
        // Convert various formats to our standard format
        $code = strtolower(str_replace('-', '_', $code));
        
        // Handle common variations
        $mappings = [
            'en_us' => 'en',
            'en_gb' => 'en',
            'zh_cn' => 'zh',
            'zh_tw' => 'zh',
            'pt_br' => 'pt',
            'pt_pt' => 'pt',
            'es_es' => 'es',
            'es_mx' => 'es',
            'fr_fr' => 'fr',
            'de_de' => 'de',
            'it_it' => 'it',
            'ru_ru' => 'ru',
            'ja_jp' => 'ja',
            'ko_kr' => 'ko',
            'ar_sa' => 'ar',
            'he_il' => 'he',
            'tr_tr' => 'tr',
            'pl_pl' => 'pl',
            'nl_nl' => 'nl',
            'sv_se' => 'sv',
            'no_no' => 'no',
            'da_dk' => 'da',
            'fi_fi' => 'fi',
            'el_gr' => 'el',
            'th_th' => 'th',
            'vi_vn' => 'vi',
            'id_id' => 'id',
            'ms_my' => 'ms',
            'tl_ph' => 'tl',
            'sw_ke' => 'sw',
            'af_za' => 'af',
            'ur_pk' => 'ur',
            'bn_bd' => 'bn',
            'ta_in' => 'ta',
            'te_in' => 'te',
            'mr_in' => 'mr',
            'gu_in' => 'gu',
            'kn_in' => 'kn',
            'ml_in' => 'ml',
            'pa_in' => 'pa',
            'or_in' => 'or',
            'as_in' => 'as'
        ];
        
        return $mappings[$code] ?? $code;
    }
}
