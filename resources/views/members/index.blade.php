@extends('layouts.app')
@section('title', 'Members')
@section('content')
<div class="page-heading"><div><p class="eyebrow">DIRECTORY</p><h1>Members</h1><p class="muted">Search and manage member records.</p></div><a href="{{ route('members.create') }}" role="button">Add member</a></div>
<form method="GET" action="{{ route('members.index') }}" class="search-form"><input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, number, category, or level" aria-label="Search members"><button type="submit">Search</button>@if(request('q'))<a href="{{ route('members.index') }}" role="button" class="outline secondary">Clear</a>@endif</form>
<div class="table-wrap"><table><thead><tr><th>Member</th><th>Category</th><th>Level</th><th>Joined</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($members as $member)
<tr><td><a href="{{ route('members.show', $member) }}"><strong>{{ $member->full_name }}</strong></a><small class="block muted">{{ $member->membership_number }} · {{ $member->email }}</small></td><td>{{ $member->category->name }}</td><td>{{ $member->level->name }}</td><td>{{ $member->joined_on?->format('M d, Y') ?? '—' }}</td><td><span class="status {{ $member->is_active ? 'status-approved' : 'status-cancelled' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td><td><a href="{{ route('members.edit', $member) }}">Edit</a></td></tr>
@empty<tr><td colspan="6" class="empty-state">No member records match this search.</td></tr>@endforelse
</tbody></table></div>
{{ $members->links() }}
@endsection
