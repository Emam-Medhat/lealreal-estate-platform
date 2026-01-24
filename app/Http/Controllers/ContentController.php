<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\News;
use App\Models\Guide;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-content');
    }

    public function dashboard(): View
    {
        $stats = [
            'blog_posts' => BlogPost::count(),
            'pages' => Page::count(),
            'news' => News::count(),
            'guides' => Guide::count(),
            'faqs' => Faq::count(),
            'published_posts' => BlogPost::where('status', 'published')->count(),
            'draft_posts' => BlogPost::where('status', 'draft')->count(),
        ];

        $recent_posts = BlogPost::latest()->take(5)->get();
        $recent_news = News::latest()->take(5)->get();

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

    public function mediaLibrary(): View
    {
        return view('content.media-library');
    }

    public function seoTools(): View
    {
        return view('content.seo-tools');
    }
}
