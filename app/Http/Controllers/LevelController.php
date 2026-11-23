<?php

namespace App\Http\Controllers;

use App\Models\MembershipLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index() { return view('levels.index', ['levels' => MembershipLevel::withCount('members')->orderBy('fee')->get()]); }
    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:membership_levels,name'], 'fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'], 'validity_months' => ['required', 'integer', 'min:1', 'max:120'], 'benefits' => ['nullable', 'string', 'max:2000']]);
        MembershipLevel::create($data);
        return back()->with('success', 'Membership level added.');
    }
    public function update(Request $request, MembershipLevel $level)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('membership_levels', 'name')->ignore($level->id)], 'fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'], 'validity_months' => ['required', 'integer', 'min:1', 'max:120'], 'benefits' => ['nullable', 'string', 'max:2000']]);
        $level->update($data);
        return back()->with('success', 'Membership level updated.');
    }
    public function toggle(MembershipLevel $level)
    {
        $level->update(['is_active' => !$level->is_active]);
        return back()->with('success', 'Membership level status updated.');
    }
}
