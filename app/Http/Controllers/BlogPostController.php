<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\ContentRevision;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-blog-posts');
    }

    public function index(Request $request): View
    {
        $posts = BlogPost::with(['author'])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        $categories = BlogCategory::all();
        
        return view('admin.blog-posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('admin.blog-posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Debug: Log the incoming request
        \Log::info('Blog post creation attempt', [
            'user_authenticated' => \Auth::check(),
            'user_id' => \Auth::id(),
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        try {
            // Pre-validation debug
            \Log::info('Starting validation', [
                'request_data' => $request->all(),
                'files' => $request->allFiles(),
                'method' => $request->method(),
                'content_length' => strlen($request->input('content', '')),
                'title_length' => strlen($request->input('title', '')),
                'category_value' => $request->input('category'),
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blog_posts',
                'content' => 'required',
                'excerpt' => 'nullable|string|max:500',
                'category' => 'required|string|exists:blog_categories,slug',
                'tags' => 'array',
                'tags.*' => 'exists:blog_tags,id',
                'featured_image' => 'nullable|image|max:2048',
                'status' => 'required|in:draft,published,archived',
                'published_at' => 'nullable|date',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'is_featured' => 'boolean',
                'allow_comments' => 'boolean',
            ]);

            \Log::info('Validation passed successfully', ['validated_data' => $validated]);

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
                \Log::info('Generated slug', ['slug' => $validated['slug']]);
            }

            $validated['author_id'] = auth()->id();
            
            if ($request->hasFile('featured_image')) {
                $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
                \Log::info('Featured image uploaded', ['path' => $validated['featured_image']]);
            }

            // Handle boolean fields
            $validated['is_featured'] = $request->boolean('is_featured', false);
            $validated['allow_comments'] = $request->boolean('allow_comments', true);

            \Log::info('About to create blog post', ['final_data' => $validated]);

            $post = BlogPost::create($validated);
            
            \Log::info('Blog post created successfully', ['post_id' => $post->id]);
            
            // Handle tags if they exist and the relationship is set up
            if (!empty($validated['tags']) && method_exists($post, 'tags')) {
                try {
                    $post->tags()->attach($validated['tags']);
                    \Log::info('Tags attached successfully', ['tags' => $validated['tags']]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to attach tags to blog post: ' . $e->getMessage());
                }
            }

            // Create initial revision if the model exists
            if (class_exists('App\Models\ContentRevision')) {
                try {
                    ContentRevision::create([
                        'content_type' => 'blog_post',
                        'content_id' => $post->id,
                        'content_data' => $post->toArray(),
                        'author_id' => auth()->id(),
                        'revision_notes' => 'إنشاء المقال',
                    ]);
                    \Log::info('Content revision created');
                } catch (\Exception $e) {
                    \Log::warning('Failed to create content revision: ' . $e->getMessage());
                }
            }

            \Log::info('Redirecting to index page');
            return redirect()->route('admin.blog.posts.index')
                ->with('success', 'تم إنشاء المقال بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed with details', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'failed_rules' => $e->validator->failed(),
                'missing_fields' => $this->getMissingRequiredFields($request)
            ]);
            
            // Get all error messages as a flat array
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }
            
            // Log specific field issues
            $this->logFieldValidationIssues($request);
            
            // Validation errors will be automatically flashed to the session
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'فشل التحقق: ' . implode(', ', $errorMessages));
        } catch (\Exception $e) {
            \Log::error('Failed to create blog post: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل إنشاء المقال: ' . $e->getMessage());
        }
    }

    /**
     * Get missing required fields
     */
    private function getMissingRequiredFields(Request $request): array
    {
        $required = ['title', 'content', 'category', 'status'];
        $missing = [];
        
        foreach ($required as $field) {
            if (empty($request->input($field))) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }

    /**
     * Log specific field validation issues
     */
    private function logFieldValidationIssues(Request $request): void
    {
        // Check title
        $title = $request->input('title', '');
        if (empty($title)) {
            \Log::error('Title field is empty');
        } elseif (strlen($title) > 255) {
            \Log::error('Title too long', ['length' => strlen($title)]);
        }

        // Check content
        $content = $request->input('content', '');
        if (empty($content)) {
            \Log::error('Content field is empty');
        }

        // Check category
        $category = $request->input('category', '');
        if (empty($category)) {
            \Log::error('Category field is empty');
        } else {
            // Check if category exists in database
            $categoryExists = \DB::table('blog_categories')->where('slug', $category)->exists();
            if (!$categoryExists) {
                \Log::error('Category does not exist', ['category' => $category]);
            }
        }

        // Check status
        $status = $request->input('status', '');
        if (empty($status)) {
            \Log::error('Status field is empty');
        } elseif (!in_array($status, ['draft', 'published', 'archived'])) {
            \Log::error('Invalid status value', ['status' => $status]);
        }

        // Check file if uploaded
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            if (!$file->isValid()) {
                \Log::error('Invalid file upload', ['error' => $file->getErrorMessage()]);
            }
        }
    }

    public function show(BlogPost $post): View
    {
        $post->load(['author', 'revisions' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.blog-posts.show', compact('post'));
    }

    public function edit(BlogPost $post): View
    {
        $post->load(['tags']);
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('admin.blog-posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,'.$post->getKey(),
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // Create revision before updating
        ContentRevision::create([
            'content_type' => 'blog_post',
            'content_id' => $post->getKey(),
            'content_data' => $post->toArray(),
            'author_id' => auth()->id(),
            'revision_notes' => $request->revision_notes ?? 'تحديث المقال',
        ]);

        $post->update($validated);
        
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.blog-posts.show', $post)
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'تم حذف المقال بنجاح');
    }

    public function duplicate(BlogPost $post): RedirectResponse
    {
        $newPost = $post->replicate();
        $newPost->title = $post->title . ' (نسخة)';
        $newPost->slug = $post->slug . '-copy-' . time();
        $newPost->status = 'draft';
        $newPost->author_id = auth()->id();
        $newPost->published_at = null;
        $newPost->save();

        // Copy tags
        $newPost->tags()->attach($post->tags->pluck('id'));

        return redirect()->route('admin.blog-posts.edit', $newPost)
            ->with('success', 'تم نسخ المقال بنجاح');
    }

    public function restore(BlogPost $post): RedirectResponse
    {
        $post->restore();

        return redirect()->route('admin.blog-posts.show', $post)
            ->with('success', 'تم استعادة المقال بنجاح');
    }
}
