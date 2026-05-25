<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort')->orderBy('id')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'scope' => 'required|in:external,internal',
            'icon'  => 'nullable|string|max:50',
            'sort'  => 'nullable|integer|min:0',
            'active'=> 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを作成しました');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'scope' => 'required|in:external,internal',
            'icon'  => 'nullable|string|max:50',
            'sort'  => 'nullable|integer|min:0',
            'active'=> 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを更新しました');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを削除しました');
    }
}
