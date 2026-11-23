<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('is_active', true)->count(),
            'pendingTransactions' => MembershipTransaction::where('status', 'pending')->count(),
            'collectedPayments' => Payment::sum('amount'),
            'recentTransactions' => MembershipTransaction::with('member')->latest()->take(6)->get(),
        ]);
    }
}
