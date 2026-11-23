<?php

namespace App\Http\Controllers;

use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index', [
            'payments' => Payment::with('transaction.member')->latest('paid_at')->paginate(12),
            'payable' => MembershipTransaction::with('member')->where('status', 'pending')->orderBy('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'membership_transaction_id' => ['required', 'exists:membership_transactions,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', Rule::in(['cash', 'bank_transfer', 'card', 'e_wallet'])],
            'paid_at' => ['required', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $transaction = MembershipTransaction::whereKey($data['membership_transaction_id'])->lockForUpdate()->firstOrFail();
            if ($transaction->status !== 'pending') {
                throw ValidationException::withMessages(['membership_transaction_id' => 'Only pending transactions can receive a payment.']);
            }
            if (round((float) $data['amount'], 2) !== round((float) $transaction->amount_due, 2)) {
                throw ValidationException::withMessages(['amount' => 'Payment amount must match the transaction amount due.']);
            }
            Payment::create([...$data, 'recorded_by' => $request->user()->id]);
            $transaction->update(['status' => 'paid']);
        });

        return redirect()->route('payments.index')->with('success', 'Payment recorded. The transaction can now be approved.');
    }
}
