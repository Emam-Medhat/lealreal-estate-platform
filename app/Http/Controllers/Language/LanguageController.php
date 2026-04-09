<?php

namespace App\Http\Controllers\Language;

use App\Http\Controllers\Controller;
use App\Services\MultiLanguageService;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    private MultiLanguageService $languageService;

    public function __construct(MultiLanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index()
    {
        $languages = Language::active()->get();
        return view('language.index', compact('languages'));
    }

    public function create()
    {
        return view('language.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:5|unique:languages',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'direction' => 'required|in:ltr,rtl',
            'locale' => 'required|string|max:10|unique:languages',
            'flag' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'category' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $language = Language::create($validated);

        return redirect()->route('language.index')
            ->with('success', 'Language created successfully.');
    }

    public function show(Language $language)
    {
        $translationStats = $this->languageService->getTranslationStats($language->code);
        $userStats = $this->languageService->getUserStats($language->code);
        $recentTranslations = $this->languageService->getRecentTranslations($language->code);

        return view('language.show', compact('language', 'translationStats', 'userStats', 'recentTranslations'));
    }

    public function edit(Language $language)
    {
        return view('language.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'direction' => 'required|in:ltr,rtl',
            'locale' => 'required|string|max:10|unique:languages,locale,' . $language->id,
            'flag' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'category' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $language->update($validated);

        return redirect()->route('language.index')
            ->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language)
    {
        $language->delete();

        return redirect()->route('language.index')
            ->with('success', 'Language deleted successfully.');
    }

    public function translate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
                'language' => 'string|size:2',
                'params' => 'array'
            ]);

            $result = $this->languageService->translate(
                $request->key,
                $request->language ?? $this->languageService->getCurrentLanguage(),
                $request->params ?? []
            );

            return response()->json([
                'success' => true,
                'translation' => $result,
                'key' => $request->key,
                'language' => $request->language ?? $this->languageService->getCurrentLanguage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Translation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setLanguage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'language' => 'required|string|size:2'
            ]);

            $result = $this->languageService->setLanguage($request->language);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCurrent(): JsonResponse
    {
        try {
            $currentLanguage = $this->languageService->getCurrentLanguage();
            $languageInfo = $this->languageService->getLanguageInfo($currentLanguage);

            return response()->json([
                'success' => true,
                'current_language' => $currentLanguage,
                'language_info' => $languageInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSupported(): JsonResponse
    {
        try {
            $languages = $this->languageService->getSupportedLanguages();

            return response()->json([
                'success' => true,
                'languages' => $languages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get supported languages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addTranslation(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
                'value' => 'required|string',
                'language' => 'required|string|size:2'
            ]);

            $result = $this->languageService->addTranslation(
                $request->key,
                $request->value,
                $request->language
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTranslations(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'language' => 'required|string|size:2',
                'group' => 'string',
                'search' => 'string'
            ]);

            $filters = $request->only(['group', 'search']);
            $result = $this->languageService->getTranslations($request->language, $filters);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function importTranslations(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'language' => 'required|string|size:2',
                'translations' => 'required|array'
            ]);

            $result = $this->languageService->importTranslations(
                $request->translations,
                $request->language
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportTranslations(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'language' => 'required|string|size:2'
            ]);

            $result = $this->languageService->exportTranslations($request->language);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(): JsonResponse
    {
        try {
            $result = $this->languageService->getLanguageStatistics();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateFiles(): JsonResponse
    {
        try {
            $result = $this->languageService->generateLanguageFiles();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate language files',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
