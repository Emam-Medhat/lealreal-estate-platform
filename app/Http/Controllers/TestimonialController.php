<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with(['user'])
            ->where('status', 'published')
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:50|max:2000',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'project_type' => 'nullable|string|max:100',
            'project_location' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'video_url' => 'nullable|url|max:500',
            'is_featured' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            $testimonialData = [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $request->content,
                'client_name' => $request->client_name,
                'client_position' => $request->client_position,
                'client_company' => $request->client_company,
                'project_type' => $request->project_type,
                'project_location' => $request->project_location,
                'rating' => $request->rating,
                'video_url' => $request->video_url,
                'featured' => $request->has('is_featured'),
                'status' => 'pending'
            ];

            // Handle client image upload
            if ($request->hasFile('client_image')) {
                $image = $request->file('client_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('testimonials', $imageName, 'public');
                $testimonialData['client_image'] = $imagePath;
            }

            $testimonial = Testimonial::create($testimonialData);

            DB::commit();

            return redirect()->route('testimonials.show', $testimonial->id)
                ->with('success', 'تم إضافة الشهادة بنجاح وجاري مراجعتها');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الشهادة: ' . $e->getMessage());
        }
    }

    public function show(Testimonial $testimonial)
    {
        $testimonial->load(['user']);

        // Get related testimonials
        $relatedTestimonials = Testimonial::where('status', 'published')
            ->where('id', '!=', $testimonial->id)
            ->when($testimonial->project_type, function($query, $type) {
                return $query->where('project_type', $type);
            })
            ->take(3)
            ->get();

        return view('testimonials.show', compact('testimonial', 'relatedTestimonials'));
    }

    public function edit(Testimonial $testimonial)
    {
        if ($testimonial->user_id !== Auth::id() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        return view('testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        if ($testimonial->user_id !== Auth::id() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:50|max:2000',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'project_type' => 'nullable|string|max:100',
            'project_location' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'video_url' => 'nullable|url|max:500',
            'is_featured' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            $testimonialData = [
                'title' => $request->title,
                'content' => $request->content,
                'client_name' => $request->client_name,
                'client_position' => $request->client_position,
                'client_company' => $request->client_company,
                'project_type' => $request->project_type,
                'project_location' => $request->project_location,
                'rating' => $request->rating,
                'video_url' => $request->video_url,
                'featured' => $request->has('is_featured')
            ];

            // Handle client image update
            if ($request->hasFile('client_image')) {
                // Delete old image
                if ($testimonial->client_image) {
                    Storage::disk('public')->delete($testimonial->client_image);
                }

                $image = $request->file('client_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('testimonials', $imageName, 'public');
                $testimonialData['client_image'] = $imagePath;
            }

            $testimonial->update($testimonialData);

            DB::commit();

            return redirect()->route('testimonials.show', $testimonial->id)
                ->with('success', 'تم تحديث الشهادة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشهادة: ' . $e->getMessage());
        }
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->user_id !== Auth::id() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        // Delete client image
        if ($testimonial->client_image) {
            Storage::disk('public')->delete($testimonial->client_image);
        }

        $testimonial->delete();

        return redirect()->route('testimonials.index')
            ->with('success', 'تم حذف الشهادة بنجاح');
    }

    public function approve(Testimonial $testimonial)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        $testimonial->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return back()->with('success', 'تم نشر الشهادة بنجاح');
    }

    public function reject(Testimonial $testimonial)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        $testimonial->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);

        return back()->with('success', 'تم رفض الشهادة');
    }

    public function feature(Testimonial $testimonial)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403);
        }

        $testimonial->update([
            'featured' => !$testimonial->featured,
            'featured_at' => $testimonial->featured ? null : now()
        ]);

        $status = $testimonial->featured ? 'تم تمييز الشهادة' : 'تم إلغاء تمييز الشهادة';
        return back()->with('success', $status);
    }

    public function myTestimonials()
    {
        $testimonials = Testimonial::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('testimonials.my-testimonials', compact('testimonials'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $testimonials = Testimonial::with(['user'])
            ->where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('client_name', 'like', "%{$query}%")
                  ->orWhere('client_company', 'like', "%{$query}%");
            })
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('testimonials.search', compact('testimonials', 'query'));
    }

    public function getFeatured()
    {
        $testimonials = Testimonial::with(['user'])
            ->where('status', 'published')
            ->where('featured', true)
            ->orderBy('featured_at', 'desc')
            ->take(6)
            ->get();

        return response()->json($testimonials);
    }

    public function getByProjectType($type)
    {
        $testimonials = Testimonial::with(['user'])
            ->where('status', 'published')
            ->where('project_type', $type)
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('testimonials.by-type', compact('testimonials', 'type'));
    }
}
