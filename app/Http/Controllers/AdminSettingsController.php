<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = AdminSettings::first();
        return response()->json(['settings' => $settings]);
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'about_us' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'zip_code' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $settings = AdminSettings::first();
        if (!$settings) {
            $settings = new AdminSettings();
        }

        // Handle file upload for logo
        if ($request->hasFile('logo')) {
            // Delete the old logo if exists
            if ($settings->logo) {
                Storage::delete($settings->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        // Update settings
        $settings->site_name = $request->site_name;
        $settings->about_us = $request->about_us;
        $settings->contact_email = $request->contact_email;
        $settings->phone = $request->phone;
        $settings->address = $request->address;
        $settings->zip_code = $request->zip_code;

        $settings->save();

        return response()->json(['message' => 'Settings updated successfully!', 'settings' => $settings]);
    }
}
