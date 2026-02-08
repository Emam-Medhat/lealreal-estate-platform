<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    private function getSettingsGroups()
    {
        return [
            'general' => [
                'name' => 'الإعدادات العامة',
                'icon' => 'fas fa-sliders-h',
                'permission' => 'manage_settings',
                'fields' => [
                    'site_name' => [
                        'label' => 'اسم الموقع',
                        'type' => 'text',
                        'rules' => 'required|string|max:255',
                        'default' => 'Real Estate Platform',
                    ],
                    'site_email' => [
                        'label' => 'البريد الإلكتروني للموقع',
                        'type' => 'email',
                        'rules' => 'required|email|max:255',
                        'default' => 'admin@example.com',
                    ],
                ]
            ],
            'users' => [
                'name' => 'إعدادات المستخدمين',
                'icon' => 'fas fa-users-cog',
                'permission' => 'manage_settings',
                'fields' => [
                    'allow_registration' => [
                        'label' => 'السماح بتسجيل المستخدمين',
                        'type' => 'toggle',
                        'rules' => 'boolean',
                        'default' => true,
                        'toggle_label' => 'تفعيل',
                        'description' => 'تمكين تسجيل مستخدمين جدد في المنصة'
                    ],
                    'email_verification' => [
                        'label' => 'التحقق من البريد الإلكتروني',
                        'type' => 'toggle',
                        'rules' => 'boolean',
                        'default' => true,
                        'toggle_label' => 'تفعيل',
                        'description' => 'تطلب التحقق من البريد الإلكتروني للحسابات الجديدة'
                    ],
                ]
            ],
            'media' => [
                'name' => 'إعدادات الوسائط',
                'icon' => 'fas fa-photo-video',
                'permission' => 'manage_settings',
                'fields' => [
                    'max_file_size' => [
                        'label' => 'أقصى حجم للملف (كيلوبايت)',
                        'type' => 'number',
                        'rules' => 'required|integer|min:1024|max:51200',
                        'default' => 10240,
                    ],
                    'supported_formats' => [
                        'label' => 'الصيغ المدعومة',
                        'type' => 'file_types',
                        'rules' => 'array',
                        'default' => ['jpg', 'jpeg', 'png', 'pdf'],
                        'options' => 'getFileTypeOptions'
                    ]
                ]
            ],
            'system' => [
                'name' => 'إعدادات النظام',
                'icon' => 'fas fa-server',
                'permission' => 'manage_settings',
                'fields' => [
                    'maintenance_mode' => [
                        'label' => 'وضع الصيانة',
                        'type' => 'toggle',
                        'rules' => 'boolean',
                        'default' => false,
                        'toggle_label' => 'تفعيل',
                        'description' => 'وضع الموقع في حالة الصيانة'
                    ]
                ]
            ]
        ];
    }

    public function getFileTypeOptions()
    {
        return [
            'images' => [
                'jpg' => 'JPG',
                'jpeg' => 'JPEG',
                'png' => 'PNG',
                'gif' => 'GIF',
                'webp' => 'WebP',
            ],
            'documents' => [
                'pdf' => 'PDF',
                'doc' => 'DOC',
                'docx' => 'DOCX',
                'xls' => 'XLS',
                'xlsx' => 'XLSX',
            ],
            'media' => [
                'mp4' => 'MP4',
                'mp3' => 'MP3',
            ],
            'archives' => [
                'zip' => 'ZIP',
                'rar' => 'RAR',
            ],
        ];
    }

    public function index()
    {
        $settingsGroups = $this->getSettingsGroups();
        $activeTab = request('tab', 'general');
        
        // Flatten default settings for the view
        $settings = [];
        foreach ($settingsGroups as $key => &$group) {
            foreach ($group['fields'] as $fieldKey => &$field) {
                $settings[$fieldKey] = $field['default'];
                
                // Resolve options if they are a method name
                if (isset($field['options']) && is_string($field['options']) && method_exists($this, $field['options'])) {
                    $field['options'] = $this->{$field['options']}();
                }
            }
        }

        // Pass controller instance for method calls in view
        $controller = $this;

        return view('admin.settings.index', compact('settings', 'settingsGroups', 'activeTab', 'controller'));
    }

    public function update(Request $request, $tab = null)
    {
        // If tab is provided, validate only that tab's fields
        $settingsGroups = $this->getSettingsGroups();
        
        if ($tab && isset($settingsGroups[$tab])) {
            $rules = [];
            foreach ($settingsGroups[$tab]['fields'] as $key => $field) {
                if (isset($field['rules'])) {
                    $rules[$key] = $field['rules'];
                }
            }
            $request->validate($rules);
        } else {
             // Fallback validation if no tab specified (legacy support)
            $request->validate([
                'site_name' => 'nullable|string|max:255',
                'site_email' => 'nullable|email|max:255',
            ]);
        }

        try {
            // Update settings logic here
            // For now, just return success message
            
            // In a real app, you would save $request->all() to a settings table or file

            return back()->with('success', 'تم تحديث الإعدادات بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الإعدادات');
        }
    }
}
