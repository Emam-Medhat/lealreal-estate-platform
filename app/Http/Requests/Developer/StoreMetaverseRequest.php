<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetaverseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:developer_projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'metaverse_type' => 'required|string|max:100',
            'platform' => 'required|string|max:100',
            'access_url' => 'nullable|url|max:500',
            'status' => 'nullable|in:draft,published,archived',
            'visibility' => 'nullable|in:public,private,restricted',
            'version' => 'nullable|string|max:20',
            'compatibility' => 'nullable|array',
            'features' => 'nullable|array',
            'assets' => 'nullable|array',
            'environments' => 'nullable|array',
            'interactions' => 'nullable|array',
            'avatar_options' => 'nullable|array',
            'navigation_options' => 'nullable|array',
            'multiplayer_enabled' => 'nullable|boolean',
            'max_concurrent_users' => 'nullable|integer|min:1|max:10000',
            'access_requirements' => 'nullable|array',
            'pricing_model' => 'nullable|string|max:100',
            'subscription_required' => 'nullable|boolean',
            'subscription_price' => 'nullable|numeric|min:0',
            'trial_period_days' => 'nullable|integer|min:0|max:365',
            'technical_specs' => 'nullable|array',
            'system_requirements' => 'nullable|array',
            'supported_devices' => 'nullable|array',
            'languages' => 'nullable|array',
            'analytics_enabled' => 'nullable|boolean',
            'privacy_settings' => 'nullable|array',
            'moderation_level' => 'nullable|string|max:50',
            'content_guidelines' => 'nullable|array',
            'integration_options' => 'nullable|array',
            'api_endpoints' => 'nullable|array',
            'webhook_urls' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'model_files' => 'nullable|array',
            'model_files.*' => 'file|mimes:fbx,obj,gltf,glb,dae,3ds,max:204800',
            'texture_files' => 'nullable|array',
            'texture_files.*' => 'file|mimes:jpg,jpeg,png,tga,dds|max:51200',
            'preview_images' => 'nullable|array',
            'preview_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'title.required' => 'Metaverse title is required.',
            'metaverse_type.required' => 'Metaverse type is required.',
            'platform.required' => 'Platform is required.',
            'access_url.url' => 'Access URL must be a valid URL.',
            'max_concurrent_users.integer' => 'Max concurrent users must be a whole number.',
            'max_concurrent_users.min' => 'Max concurrent users must be at least 1.',
            'max_concurrent_users.max' => 'Max concurrent users cannot exceed 10,000.',
            'subscription_price.numeric' => 'Subscription price must be a number.',
            'subscription_price.min' => 'Subscription price cannot be negative.',
            'trial_period_days.integer' => 'Trial period must be a whole number.',
            'trial_period_days.min' => 'Trial period cannot be negative.',
            'trial_period_days.max' => 'Trial period cannot exceed 365 days.',
            'model_files.*.mimes' => 'Model files must be FBX, OBJ, GLTF, GLB, DAE, or 3DS files.',
            'model_files.*.max' => 'Model file size cannot exceed 200MB.',
            'texture_files.*.mimes' => 'Texture files must be JPG, JPEG, PNG, TGA, or DDS files.',
            'texture_files.*.max' => 'Texture file size cannot exceed 50MB.',
            'preview_images.*.image' => 'Each file must be an image.',
            'preview_images.*.mimes' => 'Preview images must be JPEG, PNG, JPG, or GIF files.',
            'preview_images.*.max' => 'Preview image size cannot exceed 10MB.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.mimes' => 'Thumbnail must be JPEG, PNG, JPG, or GIF file.',
            'thumbnail.max' => 'Thumbnail size cannot exceed 5MB.',
        ];
    }
}
