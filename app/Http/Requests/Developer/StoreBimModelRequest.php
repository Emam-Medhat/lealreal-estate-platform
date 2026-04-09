<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class StoreBimModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:developer_projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'model_type' => 'required|in:architectural,structural,mechanical,electrical,plumbing,fire_protection',
            'software_used' => 'required|string|max:100',
            'version' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,in_review,approved,published,archived',
            'complexity_level' => 'nullable|in:low,medium,high',
            'lod_level' => 'nullable|in:100,200,300,350,400,500',
            'discipline' => 'nullable|string|max:100',
            'coordinates' => 'nullable|array',
            'metadata' => 'nullable|array',
            'parameters' => 'nullable|array',
            'materials' => 'nullable|array',
            'components' => 'nullable|array',
            'conflicts' => 'nullable|array',
            'issues' => 'nullable|array',
            'clash_results' => 'nullable|array',
            'quantities' => 'nullable|array',
            'specifications' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'model_file' => 'required|file|mimes:rvt,ifc,dwg,dxf,nwd,nwc,skp|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'additional_files' => 'nullable|array',
            'additional_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,dwg,dxf,rvt,ifc|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'name.required' => 'BIM model name is required.',
            'model_type.required' => 'Model type is required.',
            'software_used.required' => 'Software used is required.',
            'model_file.required' => 'BIM model file is required.',
            'model_file.mimes' => 'BIM model must be a supported file format (RVT, IFC, DWG, DXF, NWD, NWC, SKP).',
            'model_file.max' => 'BIM model file size cannot exceed 100MB.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.mimes' => 'Thumbnail must be JPEG, PNG, JPG, or GIF file.',
            'thumbnail.max' => 'Thumbnail size cannot exceed 5MB.',
            'additional_files.*.file' => 'Each additional file must be a file.',
            'additional_files.*.mimes' => 'Additional files must be PDF, DOC, DOCX, XLS, XLSX, DWG, DXF, RVT, or IFC files.',
            'additional_files.*.max' => 'Additional file size cannot exceed 50MB.',
        ];
    }
}
