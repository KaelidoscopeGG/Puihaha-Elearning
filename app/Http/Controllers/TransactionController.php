<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = MembershipTransaction::with(['member', 'payments'])->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))->latest()->paginate(12)->withQueryString();
        $members = Member::with('level')->where('is_active', true)->orderBy('full_name')->get();
        return view('transactions.index', compact('transactions', 'members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['member_id' => ['required', 'exists:members,id']]);
        $member = Member::with('level')->findOrFail($data['member_id']);
        MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => $member->membership_level_id,
            'type' => 'renewal',
            'status' => 'pending',
            'amount_due' => $member->level->fee,
            'created_by' => $request->user()->id,
        ]);
        return back()->with('success', 'Renewal transaction created.');
    }

    public function approve(MembershipTransaction $transaction)
    {
        if ($transaction->status !== 'paid' || !$transaction->payments()->exists()) {
            return back()->withErrors(['transaction' => 'A transaction must have a recorded payment before approval.']);
        }
        DB::transaction(function () use ($transaction) {
            $transaction->update(['status' => 'approved', 'starts_on' => today(), 'expires_on' => today()->addMonths($transaction->level->validity_months)]);
            $transaction->member->update(['is_active' => true]);
        });
        return back()->with('success', 'Paid transaction approved.');
    }

    public function cancel(MembershipTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->withErrors(['transaction' => 'Only pending transactions can be cancelled. A recorded payment needs to be resolved first.']);
        }
        $transaction->update(['status' => 'cancelled']);
        return back()->with('success', 'Transaction cancelled.');
    }
}
