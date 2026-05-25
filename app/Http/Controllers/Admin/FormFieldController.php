<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    public function index()
    {
        $fields = FormField::with('category')->orderBy('sort')->get();
        $categories = Category::orderBy('sort')->get();
        return view('admin.fields.index', compact('fields', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer',
            'label'       => 'required|string|max:100',
            'type'        => 'required|in:text,number,textarea,select',
            'options'     => 'nullable|string',
            'required'    => 'boolean',
            'hidden'      => 'boolean',
            'sort'        => 'nullable|integer|min:0',
        ]);
        $data['required'] = $request->boolean('required');
        $data['hidden']   = $request->boolean('hidden');
        if (!empty($data['options'])) {
            $data['options'] = array_filter(array_map('trim', explode("\n", $data['options'])));
        }
        FormField::create($data);
        return redirect()->route('admin.fields.index')->with('success', '項目を追加しました');
    }

    public function update(Request $request, FormField $field)
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer',
            'label'       => 'required|string|max:100',
            'type'        => 'required|in:text,number,textarea,select',
            'options'     => 'nullable|string',
            'required'    => 'boolean',
            'hidden'      => 'boolean',
            'sort'        => 'nullable|integer|min:0',
        ]);
        $data['required'] = $request->boolean('required');
        $data['hidden']   = $request->boolean('hidden');
        if (!empty($data['options'])) {
            $data['options'] = array_filter(array_map('trim', explode("\n", $data['options'])));
        }
        $field->update($data);
        return redirect()->route('admin.fields.index')->with('success', '項目を更新しました');
    }

    public function destroy(FormField $field)
    {
        $field->delete();
        return redirect()->route('admin.fields.index')->with('success', '項目を削除しました');
    }
}
