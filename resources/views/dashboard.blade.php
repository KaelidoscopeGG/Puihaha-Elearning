@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="page-heading"><div><p class="eyebrow">OVERVIEW</p><h1>Dashboard</h1><p class="muted">A quick view of membership activity.</p></div><a href="{{ route('members.create') }}" role="button">Register a member</a></div>
<section class="stats-grid">
    <article class="stat-card"><span class="stat-label">Total members</span><strong>{{ number_format($totalMembers) }}</strong><a href="{{ route('members.index') }}">View members →</a></article>
    <article class="stat-card"><span class="stat-label">Active members</span><strong>{{ number_format($activeMembers) }}</strong><span class="muted">Currently active</span></article>
    <article class="stat-card"><span class="stat-label">Pending transactions</span><strong>{{ number_format($pendingTransactions) }}</strong><a href="{{ route('transactions.index', ['status' => 'pending']) }}">Review transactions →</a></article>
    <article class="stat-card"><span class="stat-label">Payments collected</span><strong>₱{{ number_format((float) $collectedPayments, 2) }}</strong><a href="{{ route('payments.index') }}">Payment records →</a></article>
</section>
<section class="section-block">
    <div class="section-heading"><div><h2>Recent transactions</h2><p class="muted">Latest applications and renewals.</p></div><a href="{{ route('transactions.index') }}">All transactions →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Reference</th><th>Member</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>
    @forelse ($recentTransactions as $transaction)
        <tr><td><a href="{{ route('members.show', $transaction->member) }}">{{ $transaction->reference_number }}</a></td><td>{{ $transaction->member->full_name }}</td><td>{{ ucfirst($transaction->type) }}</td><td>₱{{ number_format((float) $transaction->amount_due, 2) }}</td><td><span class="status status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></td><td>{{ $transaction->created_at->format('M d, Y') }}</td></tr>
    @empty<tr><td colspan="6" class="empty-state">No transactions yet. Register a member to create the first application.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
