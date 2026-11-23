<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::with(['category', 'level'])->when($request->filled('q'), function ($query) use ($request) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('membership_number', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('level', fn ($l) => $l->where('name', 'like', "%{$search}%"));
            });
        })->latest()->paginate(10)->withQueryString();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create', [
            'categories' => MembershipCategory::where('is_active', true)->orderBy('name')->get(),
            'levels' => MembershipLevel::where('is_active', true)->orderBy('fee')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['joined_on'] = now()->toDateString();
        $data['is_active'] = true;
        DB::transaction(function () use ($data, $request) {
            $member = Member::create($data);
            MembershipTransaction::create([
                'member_id' => $member->id,
                'membership_level_id' => $member->membership_level_id,
                'type' => 'application',
                'status' => 'pending',
                'amount_due' => $member->level->fee,
                'created_by' => $request->user()->id,
            ]);
        });
        return redirect()->route('members.index')->with('success', 'Member registered. An application transaction is now pending.');
    }

    public function show(Member $member)
    {
        $member->load(['category', 'level', 'transactions.payments']);
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', [
            'member' => $member,
            'categories' => MembershipCategory::orderBy('name')->get(),
            'levels' => MembershipLevel::orderBy('fee')->get(),
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $member->update($this->validated($request, $member));
        return redirect()->route('members.show', $member)->with('success', 'Member details updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deactivated and archived.');
    }

    private function validated(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($member?->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'membership_category_id' => ['required', 'exists:membership_categories,id'],
            'membership_level_id' => ['required', 'exists:membership_levels,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
