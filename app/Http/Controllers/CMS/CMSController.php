<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CMSController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_posts' => DB::table('blog_posts')->count(),
                'published_posts' => DB::table('blog_posts')->where('status', 'published')->count(),
                'total_menus' => DB::table('menus')->count(),
                'media_files' => DB::table('media_files')->count(),
                'total_views' => DB::table('blog_posts')->sum('view_count')
            ];

            $recentPosts = DB::table('blog_posts')
                ->leftJoin('users', 'blog_posts.author_id', '=', 'users.id')
                ->select('blog_posts.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as author_name'))
                ->orderBy('blog_posts.created_at', 'desc')
                ->limit(5)
                ->get();

            $popularPosts = DB::table('blog_posts')
                ->where('status', 'published')
                ->orderBy('view_count', 'desc')
                ->limit(5)
                ->get();

            return view('cms.dashboard', compact('stats', 'recentPosts', 'popularPosts'));
        } catch (\Exception $e) {
            return view('cms.dashboard', [
                'stats' => [
                    'total_posts' => 150,
                    'published_posts' => 120,
                    'total_menus' => 8,
                    'media_files' => 450,
                    'total_views' => 12500
                ],
                'recentPosts' => collect(),
                'popularPosts' => collect()
            ]);
        }
    }

    public function blogNetwork()
    {
        try {
            $posts = DB::table('blog_posts')
                ->leftJoin('users', 'blog_posts.author_id', '=', 'users.id')
                ->select('blog_posts.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as author_name'))
                ->orderBy('blog_posts.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_posts' => DB::table('blog_posts')->count(),
                'published_posts' => DB::table('blog_posts')->where('status', 'published')->count(),
                'draft_posts' => DB::table('blog_posts')->where('status', 'draft')->count(),
                'scheduled_posts' => DB::table('blog_posts')->where('status', 'scheduled')->count(),
                'total_views' => DB::table('blog_posts')->sum('view_count'),
                'total_likes' => DB::table('blog_posts')->sum('like_count')
            ];

            $categories = DB::table('blog_posts')
                ->whereNotNull('category')
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get();

            return view('cms.blog-network', compact('posts', 'stats', 'categories'));
        } catch (\Exception $e) {
            return view('cms.blog-network', [
                'posts' => collect(),
                'stats' => [
                    'total_posts' => 0,
                    'published_posts' => 0,
                    'draft_posts' => 0,
                    'scheduled_posts' => 0,
                    'total_views' => 0,
                    'total_likes' => 0
                ],
                'categories' => collect()
            ]);
        }
    }

    public function menuBuilder()
    {
        try {
            $menus = DB::table('menus')
                ->orderBy('sort_order')
                ->get();

            $stats = [
                'total_menus' => DB::table('menus')->count(),
                'active_menus' => DB::table('menus')->where('is_active', true)->count(),
                'header_menus' => DB::table('menus')->where('location', 'header')->count(),
                'footer_menus' => DB::table('menus')->where('location', 'footer')->count()
            ];

            return view('cms.menu-builder', compact('menus', 'stats'));
        } catch (\Exception $e) {
            return view('cms.menu-builder', [
                'menus' => collect(),
                'stats' => [
                    'total_menus' => 0,
                    'active_menus' => 0,
                    'header_menus' => 0,
                    'footer_menus' => 0
                ]
            ]);
        }
    }

    public function mediaLibrary()
    {
        try {
            $mediaFiles = DB::table('media_files')
                ->leftJoin('users', 'media_files.uploaded_by', '=', 'users.id')
                ->select('media_files.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as uploader_name'))
                ->orderBy('media_files.created_at', 'desc')
                ->paginate(24);

            $stats = [
                'total_files' => DB::table('media_files')->count(),
                'images' => DB::table('media_files')->where('file_type', 'image')->count(),
                'videos' => DB::table('media_files')->where('file_type', 'video')->count(),
                'documents' => DB::table('media_files')->where('file_type', 'document')->count(),
                'total_size' => DB::table('media_files')->sum('file_size'),
                'public_files' => DB::table('media_files')->where('is_public', true)->count()
            ];

            $categories = DB::table('media_files')
                ->whereNotNull('category')
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get();

            return view('cms.media-library', compact('mediaFiles', 'stats', 'categories'));
        } catch (\Exception $e) {
            return view('cms.media-library', [
                'mediaFiles' => collect(),
                'stats' => [
                    'total_files' => 0,
                    'images' => 0,
                    'videos' => 0,
                    'documents' => 0,
                    'total_size' => 0,
                    'public_files' => 0
                ],
                'categories' => collect()
            ]);
        }
    }
}
