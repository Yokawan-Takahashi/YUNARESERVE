<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = app('tenant') ?? auth()->user()->tenant;
        return view('admin.settings.index', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenant = app('tenant') ?? auth()->user()->tenant;

        $validated = $request->validate([
            'company_name'          => 'required|string|max:100',
            'notify_email'          => 'nullable|email:rfc|max:200',
            'color'                 => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo'                  => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
            'cancel_deadline_days'  => 'nullable|integer|min:0|max:365',
            'privacy_policy_url'    => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('logo')) {
            // 旧ロゴを削除
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            $path = $request->file('logo')->store("logos/{$tenant->id}", 'public');
            $validated['logo_path'] = $path;
        }

        unset($validated['logo']);

        $tenant->update($validated);

        return redirect()->route('admin.settings.index')->with('success', '設定を保存しました。');
    }
}
