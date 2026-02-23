<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\News;
use App\Models\Guide;
use App\Models\Faq;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard(): View
    {
        $stats = [
            'blog_posts' => $this->safeCount('BlogPost'),
            'pages' => $this->safeCount('Page'),
            'news' => $this->safeCount('News'),
            'guides' => $this->safeCount('Guide'),
            'faqs' => $this->safeCount('Faq'),
            'published_posts' => $this->safeCountWhere('BlogPost', 'status', 'published'),
            'draft_posts' => $this->safeCountWhere('BlogPost', 'status', 'draft'),
        ];

        $recent_posts = $this->safeGet('BlogPost', 5);
        $recent_news = $this->safeGet('News', 5);

        return view('content.dashboard', compact('stats', 'recent_posts', 'recent_news'));
    }

    public function search(Request $request): View
    {
        $query = $request->get('q');
        
        $blog_posts = BlogPost::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->get();
            
        $pages = Page::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->get();
            
        $news = News::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->get();

        return view('content.search', compact('query', 'blog_posts', 'pages', 'news'));
    }

    public function mediaLibrary(Request $request): View
    {
        $query = MediaFile::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where('filename', 'like', "%{$request->search}%")
                  ->orWhere('alt_text', 'like', "%{$request->search}%");
        }

        $mediaFiles = $query->latest()->paginate(24);
        $types = MediaFile::distinct()->pluck('type');

        return view('admin.media.index', compact('mediaFiles', 'types'));
    }

    public function seoTools(): View
    {
        return view('content.seo-tools');
    }

    /**
     * Safely count records from a table that might not exist
     */
    private function safeCount($model)
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass)) {
                return $modelClass::count();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
        }
        return 0;
    }

    /**
     * Safely count records with where condition from a table that might not exist
     */
    private function safeCountWhere($model, $column, $value)
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass)) {
                return $modelClass::where($column, $value)->count();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
        }
        return 0;
    }

    /**
     * Safely get records from a table that might not exist
     */
    private function safeGet($model, $limit = 5)
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass)) {
                return $modelClass::latest()->take($limit)->get();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
        }
        return collect();
    }
}
