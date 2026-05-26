<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $staff  = User::where('tenant_id', $tenant->id)->orderBy('role')->orderBy('name')->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['admin', 'staff', 'viewer'])],
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
        ]);

        return back()->with('success', 'スタッフを追加しました。');
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorizeStaff($user);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'staff', 'viewer'])],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'ロールを更新しました。');
    }

    public function destroy(User $user)
    {
        $this->authorizeStaff($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', '自分自身は削除できません。');
        }

        $user->delete();

        return back()->with('success', 'スタッフを削除しました。');
    }

    private function authorizeStaff(User $user): void
    {
        $tenantId = auth()->user()->tenant_id;
        abort_if($user->tenant_id !== $tenantId, 403);
        abort_if($user->role === 'owner', 403, 'オーナーアカウントは変更できません。');
    }
}
