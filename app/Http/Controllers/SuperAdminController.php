<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    private const INDUSTRY_TEMPLATES = [
        '学習塾・教室'       => ['講座', 'セミナー', 'テスト'],
        '整体・サロン'        => ['施術', 'カウンセリング', 'ご相談'],
        'カルチャースクール' => ['体験クラス', 'レギュラークラス', 'ワークショップ'],
        'スポーツジム'        => ['体験入会', 'パーソナルトレーニング', 'グループレッスン'],
        'その他'              => ['イベント', 'セミナー', 'ご相談'],
    ];

    public function index()
    {
        $tenants = Tenant::withoutGlobalScopes()->with('users')->latest()->paginate(30);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $industries = array_keys(self::INDUSTRY_TEMPLATES);
        return view('superadmin.tenants.create', compact('industries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'   => 'required|string|max:100',
            'slug'           => 'required|string|max:50|unique:tenants,slug|alpha_dash',
            'industry'       => 'required|string',
            'owner_name'     => 'required|string|max:100',
            'owner_email'    => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'company_name' => $validated['company_name'],
                'slug'         => $validated['slug'],
                'industry'     => $validated['industry'],
                'status'       => 'active',
                'color'        => '#4f46e5',
            ]);

            User::create([
                'tenant_id' => $tenant->id,
                'name'      => $validated['owner_name'],
                'email'     => $validated['owner_email'],
                'password'  => Hash::make($validated['owner_password']),
                'role'      => 'owner',
            ]);
        });

        return redirect()->route('superadmin.tenants.index')->with('success', 'テナントを作成しました。');
    }

    public function edit(Tenant $tenant)
    {
        $industries = array_keys(self::INDUSTRY_TEMPLATES);
        return view('superadmin.tenants.edit', compact('tenant', 'industries'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
            'notify_email' => 'nullable|email|max:200',
            'status'       => 'required|in:active,suspended',
            'industry'     => 'nullable|string|max:100',
        ]);

        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'テナント情報を更新しました。');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update([
            'status' => $tenant->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'テナントのステータスを変更しました。');
    }
}
