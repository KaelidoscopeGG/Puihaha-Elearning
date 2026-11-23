<?php

namespace App\Http\Controllers;

use App\Models\MembershipCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index() { return view('categories.index', ['categories' => MembershipCategory::withCount('members')->orderBy('name')->get()]); }
    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:membership_categories,name'], 'description' => ['nullable', 'string', 'max:1000']]);
        MembershipCategory::create($data);
        return back()->with('success', 'Category added.');
    }
    public function update(Request $request, MembershipCategory $category)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('membership_categories', 'name')->ignore($category->id)], 'description' => ['nullable', 'string', 'max:1000']]);
        $category->update($data);
        return back()->with('success', 'Category updated.');
    }
    public function toggle(MembershipCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return back()->with('success', 'Category status updated.');
    }
}
