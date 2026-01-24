<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\PropertyBrochure;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class PropertyBrochureController extends Controller
{
    /**
     * Display a listing of property brochures.
     */
    public function index()
    {
        $brochures = PropertyBrochure::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Brochure/Index', [
            'brochures' => $brochures,
            'stats' => [
                'total_brochures' => PropertyBrochure::count(),
                'published_brochures' => PropertyBrochure::where('status', 'published')->count(),
                'draft_brochures' => PropertyBrochure::where('status', 'draft')->count(),
                'total_downloads' => PropertyBrochure::sum('download_count'),
                'total_views' => PropertyBrochure::sum('view_count'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new property brochure.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Brochure/Create', [
            'properties' => $properties,
            'templates' => ['modern', 'classic', 'luxury', 'minimal', 'corporate'],
            'formats' => ['a4', 'a5', 'letter', 'legal', 'square'],
            'orientations' => ['portrait', 'landscape'],
        ]);
    }

    /**
     * Store a newly created property brochure.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template' => 'required|string|in:modern,classic,luxury,minimal,corporate',
            'format' => 'required|string|in:a4,a5,letter,legal,square',
            'orientation' => 'required|string|in:portrait,landscape',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'contact_info' => 'nullable|array',
            'contact_info.name' => 'nullable|string|max:255',
            'contact_info.phone' => 'nullable|string|max:20',
            'contact_info.email' => 'nullable|email|max:255',
            'contact_info.website' => 'nullable|string|max:255',
            'pricing_info' => 'nullable|array',
            'pricing_info.price' => 'nullable|numeric|min:0',
            'pricing_info.currency' => 'nullable|string|max:3',
            'pricing_info.price_type' => 'nullable|string|in:sale,rent',
            'custom_colors' => 'nullable|array',
            'custom_colors.primary' => 'nullable|string|max:7',
            'custom_colors.secondary' => 'nullable|string|max:7',
            'custom_colors.accent' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:255',
            'include_floor_plans' => 'boolean',
            'include_location_map' => 'boolean',
            'include_qr_code' => 'boolean',
        ]);

        $brochure = PropertyBrochure::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'template' => $validated['template'],
            'format' => $validated['format'],
            'orientation' => $validated['orientation'],
            'features' => $validated['features'] ?? [],
            'amenities' => $validated['amenities'] ?? [],
            'contact_info' => $validated['contact_info'] ?? [],
            'pricing_info' => $validated['pricing_info'] ?? [],
            'custom_colors' => $validated['custom_colors'] ?? [],
            'font_family' => $validated['font_family'] ?? null,
            'include_floor_plans' => $validated['include_floor_plans'] ?? false,
            'include_location_map' => $validated['include_location_map'] ?? false,
            'include_qr_code' => $validated['include_qr_code'] ?? false,
            'status' => 'draft',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('brochure-covers', 'public');
            $brochure->update(['cover_image' => $path]);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brochure-logos', 'public');
            $brochure->update(['logo' => $path]);
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('brochure-galleries', 'public');
                $galleryPaths[] = $path;
            }
            $brochure->update(['gallery_images' => json_encode($galleryPaths)]);
        }

        return redirect()->route('marketing.brochure.index')
            ->with('success', 'تم إنشاء الكتيب بنجاح');
    }

    /**
     * Display the specified property brochure.
     */
    public function show(PropertyBrochure $propertyBrochure)
    {
        $propertyBrochure->load(['property']);

        return Inertia::render('Marketing/Brochure/Show', [
            'brochure' => $propertyBrochure,
            'analytics' => $this->getBrochureAnalytics($propertyBrochure),
        ]);
    }

    /**
     * Show the form for editing the specified property brochure.
     */
    public function edit(PropertyBrochure $propertyBrochure)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Brochure/Edit', [
            'brochure' => $propertyBrochure,
            'properties' => $properties,
            'templates' => ['modern', 'classic', 'luxury', 'minimal', 'corporate'],
            'formats' => ['a4', 'a5', 'letter', 'legal', 'square'],
            'orientations' => ['portrait', 'landscape'],
        ]);
    }

    /**
     * Update the specified property brochure.
     */
    public function update(Request $request, PropertyBrochure $propertyBrochure)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template' => 'required|string|in:modern,classic,luxury,minimal,corporate',
            'format' => 'required|string|in:a4,a5,letter,legal,square',
            'orientation' => 'required|string|in:portrait,landscape',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'contact_info' => 'nullable|array',
            'contact_info.name' => 'nullable|string|max:255',
            'contact_info.phone' => 'nullable|string|max:20',
            'contact_info.email' => 'nullable|email|max:255',
            'contact_info.website' => 'nullable|string|max:255',
            'pricing_info' => 'nullable|array',
            'pricing_info.price' => 'nullable|numeric|min:0',
            'pricing_info.currency' => 'nullable|string|max:3',
            'pricing_info.price_type' => 'nullable|string|in:sale,rent',
            'custom_colors' => 'nullable|array',
            'custom_colors.primary' => 'nullable|string|max:7',
            'custom_colors.secondary' => 'nullable|string|max:7',
            'custom_colors.accent' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:255',
            'include_floor_plans' => 'boolean',
            'include_location_map' => 'boolean',
            'include_qr_code' => 'boolean',
        ]);

        $propertyBrochure->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'template' => $validated['template'],
            'format' => $validated['format'],
            'orientation' => $validated['orientation'],
            'features' => $validated['features'] ?? [],
            'amenities' => $validated['amenities'] ?? [],
            'contact_info' => $validated['contact_info'] ?? [],
            'pricing_info' => $validated['pricing_info'] ?? [],
            'custom_colors' => $validated['custom_colors'] ?? [],
            'font_family' => $validated['font_family'] ?? null,
            'include_floor_plans' => $validated['include_floor_plans'] ?? false,
            'include_location_map' => $validated['include_location_map'] ?? false,
            'include_qr_code' => $validated['include_qr_code'] ?? false,
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image
            if ($propertyBrochure->cover_image) {
                Storage::disk('public')->delete($propertyBrochure->cover_image);
            }
            $path = $request->file('cover_image')->store('brochure-covers', 'public');
            $propertyBrochure->update(['cover_image' => $path]);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($propertyBrochure->logo) {
                Storage::disk('public')->delete($propertyBrochure->logo);
            }
            $path = $request->file('logo')->store('brochure-logos', 'public');
            $propertyBrochure->update(['logo' => $path]);
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($propertyBrochure->gallery_images) {
                $oldGalleryImages = json_decode($propertyBrochure->gallery_images, true);
                foreach ($oldGalleryImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('brochure-galleries', 'public');
                $galleryPaths[] = $path;
            }
            $propertyBrochure->update(['gallery_images' => json_encode($galleryPaths)]);
        }

        return redirect()->route('marketing.brochure.index')
            ->with('success', 'تم تحديث الكتيب بنجاح');
    }

    /**
     * Remove the specified property brochure.
     */
    public function destroy(PropertyBrochure $propertyBrochure)
    {
        // Delete associated files
        if ($propertyBrochure->cover_image) {
            Storage::disk('public')->delete($propertyBrochure->cover_image);
        }
        if ($propertyBrochure->logo) {
            Storage::disk('public')->delete($propertyBrochure->logo);
        }
        if ($propertyBrochure->gallery_images) {
            $galleryImages = json_decode($propertyBrochure->gallery_images, true);
            foreach ($galleryImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($propertyBrochure->pdf_file) {
            Storage::disk('public')->delete($propertyBrochure->pdf_file);
        }

        $propertyBrochure->delete();

        return redirect()->route('marketing.brochure.index')
            ->with('success', 'تم حذف الكتيب بنجاح');
    }

    /**
     * Generate PDF for the brochure.
     */
    public function generatePdf(PropertyBrochure $propertyBrochure)
    {
        try {
            $propertyBrochure->load(['property']);
            
            $pdf = PDF::loadView('marketing.brochure.pdf', [
                'brochure' => $propertyBrochure,
                'property' => $propertyBrochure->property,
            ]);

            $filename = 'brochure-' . $propertyBrochure->id . '-' . date('Y-m-d') . '.pdf';
            $path = 'brochure-pdfs/' . $filename;
            
            // Save PDF to storage
            Storage::disk('public')->put($path, $pdf->output());
            
            // Update brochure with PDF path
            $propertyBrochure->update([
                'pdf_file' => $path,
                'status' => 'published',
                'generated_at' => now(),
            ]);

            return back()->with('success', 'تم إنشاء ملف PDF بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إنشاء ملف PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download the brochure PDF.
     */
    public function download(PropertyBrochure $propertyBrochure)
    {
        if (!$propertyBrochure->pdf_file) {
            return back()->with('error', 'لا يوجد ملف PDF متاح');
        }

        // Increment download count
        $propertyBrochure->increment('download_count');

        return Storage::disk('public')->download($propertyBrochure->pdf_file);
    }

    /**
     * Preview the brochure PDF.
     */
    public function preview(PropertyBrochure $propertyBrochure)
    {
        if (!$propertyBrochure->pdf_file) {
            return back()->with('error', 'لا يوجد ملف PDF متاح');
        }

        // Increment view count
        $propertyBrochure->increment('view_count');

        return response()->file(Storage::disk('public')->path($propertyBrochure->pdf_file));
    }

    /**
     * Duplicate a property brochure.
     */
    public function duplicate(PropertyBrochure $propertyBrochure)
    {
        $newBrochure = $propertyBrochure->replicate();
        $newBrochure->title = $propertyBrochure->title . ' (نسخة)';
        $newBrochure->status = 'draft';
        $newBrochure->pdf_file = null;
        $newBrochure->generated_at = null;
        $newBrochure->download_count = 0;
        $newBrochure->view_count = 0;
        $newBrochure->save();

        return redirect()->route('marketing.brochure.edit', $newBrochure)
            ->with('success', 'تم نسخ الكتيب بنجاح');
    }

    /**
     * Get analytics for a brochure.
     */
    public function analytics(PropertyBrochure $propertyBrochure)
    {
        $analytics = $this->getBrochureAnalytics($propertyBrochure);

        return Inertia::render('Marketing/Brochure/Analytics', [
            'brochure' => $propertyBrochure,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Export brochure data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $brochures = PropertyBrochure::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'property-brochures-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($brochures) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'العقار', 'القالب', 'الحالة', 
                    'التنسيق', 'الاتجاه', 'عدد التحميلات', 'عدد المشاهدات', 'تاريخ الإنشاء'
                ]);

                // CSV Data
                foreach ($brochures as $brochure) {
                    fputcsv($file, [
                        $brochure->id,
                        $brochure->title,
                        $brochure->property?->title ?? 'N/A',
                        $brochure->template,
                        $brochure->status,
                        $brochure->format,
                        $brochure->orientation,
                        $brochure->download_count,
                        $brochure->view_count,
                        $brochure->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get brochure analytics data.
     */
    private function getBrochureAnalytics(PropertyBrochure $brochure)
    {
        // Mock analytics data
        return [
            'download_trend' => [
                'last_7_days' => rand(10, 100),
                'last_30_days' => rand(50, 500),
                'last_90_days' => rand(200, 1500),
            ],
            'view_trend' => [
                'last_7_days' => rand(50, 500),
                'last_30_days' => rand(200, 2000),
                'last_90_days' => rand(800, 6000),
            ],
            'conversion_rate' => rand(5, 25) . '%',
            'average_time_spent' => rand(2, 10) . ' minutes',
            'bounce_rate' => rand(20, 60) . '%',
            'device_breakdown' => [
                'desktop' => rand(40, 70),
                'mobile' => rand(20, 40),
                'tablet' => rand(10, 20),
            ],
            'location_breakdown' => [
                'الرياض' => rand(20, 40),
                'جدة' => rand(15, 30),
                'الدمام' => rand(10, 25),
                'مكة' => rand(8, 20),
                'أخرى' => rand(5, 15),
            ],
            'popular_templates' => [
                'modern' => PropertyBrochure::where('template', 'modern')->count(),
                'classic' => PropertyBrochure::where('template', 'classic')->count(),
                'luxury' => PropertyBrochure::where('template', 'luxury')->count(),
                'minimal' => PropertyBrochure::where('template', 'minimal')->count(),
                'corporate' => PropertyBrochure::where('template', 'corporate')->count(),
            ],
        ];
    }
}
