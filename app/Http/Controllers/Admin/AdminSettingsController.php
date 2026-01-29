<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Get system settings (fallback data)
        $settings = [
            'site_name' => 'Real Estate Platform',
            'site_email' => 'admin@example.com',
            'maintenance_mode' => false,
            'allow_registration' => true,
            'email_verification' => true,
            'max_file_size' => 10240, // KB
            'supported_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validation
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email|max:255',
            'maintenance_mode' => 'boolean',
            'allow_registration' => 'boolean',
            'email_verification' => 'boolean',
            'max_file_size' => 'required|integer|min:1024|max:51200',
        ]);

        try {
            // Update settings logic here
            // For now, just return success message

            return back()->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings');
        }
    }
}
