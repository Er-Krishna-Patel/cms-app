<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class SettingsController extends \App\Http\Controllers\Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('role:admin'),
        ];
    }

    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $rules = [
            'site_title' => 'required|string|max:100',
            'site_tagline' => 'nullable|string|max:200',
            'site_url' => 'required|url',
            'admin_email' => 'required|email',
            'posts_per_page' => 'required|integer|min:1|max:100',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'posts_use_categories' => 'nullable|boolean',
            'require_post_category' => 'nullable|boolean',
            'enable_comments' => 'nullable|boolean',
            'moderate_comments' => 'nullable|boolean',
            
            // Media settings
            'media_generate_thumbnails' => 'nullable|boolean',
            'media_convert_to_webp' => 'nullable|boolean',
            'media_webp_quality' => 'nullable|integer|min:1|max:100',
            'media_thumb_width' => 'nullable|integer|min:50|max:2000',
            'media_thumb_height' => 'nullable|integer|min:50|max:2000',
            'media_medium_width' => 'nullable|integer|min:100|max:3000',
            'media_medium_height' => 'nullable|integer|min:100|max:3000',
            'media_large_width' => 'nullable|integer|min:200|max:5000',
            'media_large_height' => 'nullable|integer|min:200|max:5000',
            'media_max_upload_size' => 'nullable|integer|min:1024|max:102400',
        ];

        $request->validate($rules);

        $settings = [
            'site_title' => ['value' => $request->input('site_title'), 'group' => 'general'],
            'site_tagline' => ['value' => $request->input('site_tagline'), 'group' => 'general'],
            'site_url' => ['value' => $request->input('site_url'), 'group' => 'general'],
            'admin_email' => ['value' => $request->input('admin_email'), 'group' => 'general'],
            'posts_per_page' => ['value' => $request->input('posts_per_page'), 'group' => 'reading', 'type' => 'integer'],
            'timezone' => ['value' => $request->input('timezone'), 'group' => 'general'],
            'date_format' => ['value' => $request->input('date_format'), 'group' => 'general'],
            'posts_use_categories' => ['value' => $request->input('posts_use_categories') ? 'true' : 'false', 'group' => 'writing', 'type' => 'boolean'],
            'require_post_category' => ['value' => $request->input('require_post_category') ? 'true' : 'false', 'group' => 'writing', 'type' => 'boolean'],
            'enable_comments' => ['value' => $request->input('enable_comments') ? 'true' : 'false', 'group' => 'writing', 'type' => 'boolean'],
            'moderate_comments' => ['value' => $request->input('moderate_comments') ? 'true' : 'false', 'group' => 'writing', 'type' => 'boolean'],
            
            // Media settings
            'media_generate_thumbnails' => ['value' => $request->input('media_generate_thumbnails') ? 'true' : 'false', 'group' => 'media', 'type' => 'boolean'],
            'media_convert_to_webp' => ['value' => $request->input('media_convert_to_webp') ? 'true' : 'false', 'group' => 'media', 'type' => 'boolean'],
            'media_webp_quality' => ['value' => $request->input('media_webp_quality', '85'), 'group' => 'media', 'type' => 'integer'],
            'media_thumb_width' => ['value' => $request->input('media_thumb_width', '300'), 'group' => 'media', 'type' => 'integer'],
            'media_thumb_height' => ['value' => $request->input('media_thumb_height', '300'), 'group' => 'media', 'type' => 'integer'],
            'media_medium_width' => ['value' => $request->input('media_medium_width', '600'), 'group' => 'media', 'type' => 'integer'],
            'media_medium_height' => ['value' => $request->input('media_medium_height', '600'), 'group' => 'media', 'type' => 'integer'],
            'media_large_width' => ['value' => $request->input('media_large_width', '1200'), 'group' => 'media', 'type' => 'integer'],
            'media_large_height' => ['value' => $request->input('media_large_height', '1200'), 'group' => 'media', 'type' => 'integer'],
            'media_max_upload_size' => ['value' => $request->input('media_max_upload_size', '10240'), 'group' => 'media', 'type' => 'integer'],
        ];

        foreach ($settings as $key => $data) {
            Setting::set($key, $data['value'], $data['group'] ?? 'general', $data['type'] ?? 'string');
        }

        ActivityLog::log('updated', 'Settings', 0, 'Updated site settings');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
