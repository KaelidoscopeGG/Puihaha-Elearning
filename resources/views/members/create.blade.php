@extends('layouts.app')
@section('title', 'Register member')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MEMBER RECORD</p><h1>Register a member</h1><p class="muted">A pending application transaction will be created automatically.</p></div></div>
<article class="form-card">@include('members._form', ['member' => new App\Models\Member()])</article>
@endsection
