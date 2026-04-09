<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'project_name' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'completion_date' => 'required|date',
            'project_value' => 'nullable|numeric|min:0',
            'project_area' => 'nullable|numeric|min:0',
            'project_type' => 'required|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'architect' => 'nullable|string|max:255',
            'contractor' => 'nullable|string|max:255',
            'consultants' => 'nullable|array',
            'consultants.*' => 'string|max:255',
            'key_features' => 'nullable|array',
            'key_features.*' => 'string|max:500',
            'challenges' => 'nullable|array',
            'challenges.*' => 'string|max:500',
            'solutions' => 'nullable|array',
            'solutions.*' => 'string|max:500',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:255',
            'materials' => 'nullable|array',
            'materials.*' => 'string|max:255',
            'awards' => 'nullable|array',
            'awards.*.name' => 'required|string|max:255',
            'awards.*.year' => 'required|integer|min:1900|max:' . date('Y'),
            'awards.*.organization' => 'required|string|max:255',
            'testimonials' => 'nullable|array',
            'testimonials.*.client_name' => 'required|string|max:255',
            'testimonials.*.testimonial' => 'required|string|max:1000',
            'testimonials.*.date' => 'required|date',
            'media_coverage' => 'nullable|array',
            'media_coverage.*.outlet' => 'required|string|max:255',
            'media_coverage.*.title' => 'required|string|max:500',
            'media_coverage.*.date' => 'required|date',
            'media_coverage.*.url' => 'nullable|url|max:500',
            'sustainability_features' => 'nullable|array',
            'sustainability_features.*' => 'string|max:500',
            'innovation_highlights' => 'nullable|array',
            'innovation_highlights.*' => 'string|max:500',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:51200',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Portfolio title is required.',
            'description.required' => 'Portfolio description is required.',
            'category.required' => 'Portfolio category is required.',
            'project_name.required' => 'Project name is required.',
            'location.required' => 'Project location is required.',
            'completion_date.required' => 'Completion date is required.',
            'completion_date.date' => 'Completion date must be a valid date.',
            'project_type.required' => 'Project type is required.',
            'status.required' => 'Portfolio status is required.',
            'project_value.numeric' => 'Project value must be a number.',
            'project_value.min' => 'Project value cannot be negative.',
            'project_area.numeric' => 'Project area must be a number.',
            'project_area.min' => 'Project area cannot be negative.',
            'awards.*.name.required' => 'Award name is required.',
            'awards.*.year.required' => 'Award year is required.',
            'awards.*.year.integer' => 'Award year must be a number.',
            'awards.*.year.min' => 'Award year cannot be before 1900.',
            'awards.*.year.max' => 'Award year cannot be in the future.',
            'awards.*.organization.required' => 'Award organization is required.',
            'testimonials.*.client_name.required' => 'Client name is required.',
            'testimonials.*.testimonial.required' => 'Testimonial is required.',
            'testimonials.*.date.required' => 'Testimonial date is required.',
            'testimonials.*.date.date' => 'Testimonial date must be a valid date.',
            'media_coverage.*.outlet.required' => 'Media outlet is required.',
            'media_coverage.*.title.required' => 'Media title is required.',
            'media_coverage.*.date.required' => 'Media date is required.',
            'media_coverage.*.date.date' => 'Media date must be a valid date.',
            'media_coverage.*.url.url' => 'Media URL must be a valid URL.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, or GIF files.',
            'cover_image.image' => 'Cover image must be an image file.',
            'cover_image.mimes' => 'Cover image must be a JPEG, PNG, JPG, or GIF file.',
            'cover_image.max' => 'Cover image size cannot exceed 5MB.',
            'video.file' => 'Video must be a file.',
            'video.mimes' => 'Video must be MP4, AVI, MOV, or WMV file.',
            'video.max' => 'Video size cannot exceed 50MB.',
            'documents.*.file' => 'Each document must be a file.',
            'documents.*.mimes' => 'Documents must be PDF, DOC, DOCX, PPT, or PPTX files.',
            'documents.*.max' => 'Document size cannot exceed 10MB.',
        ];
    }
}
