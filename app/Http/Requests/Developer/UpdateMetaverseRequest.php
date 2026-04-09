<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMetaverseRequest extends FormRequest
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
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,private,restricted',
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
            'texture_files.*' => 'file|mimes:jpg,jpeg,png,tga,dds,max:51200',
            'preview_images' => 'nullable|array',
            'preview_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'title.required' => 'Metaverse title is required.',
            'metaverse_type.required' => 'Metaverse type is required.',
            'platform.required' => 'Platform is required.',
            'status.required' => 'Metaverse status is required.',
            'visibility.required' => 'Visibility is required.',
        ];
    }
}
