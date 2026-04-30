<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class PlatformSettingsController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::all()->pluck('value', 'key');
        return view('admin.platform-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'facebook_url'  => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'tiktok_url'    => 'nullable|url|max:255',
            'youtube_url'   => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            PlatformSetting::setValue($key, $value);
        }

        return back()->with('success', 'Setările platformei au fost salvate cu succes!');
    }
}
