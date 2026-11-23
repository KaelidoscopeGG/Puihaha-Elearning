@extends('layouts.app')
@section('title', 'Edit member')
@section('content')
<div class="page-heading"><div><p class="eyebrow">{{ $member->membership_number }}</p><h1>Edit member</h1><p class="muted">Update contact details, category, level, or active status.</p></div></div>
<article class="form-card">@include('members._form')</article>
@endsection
