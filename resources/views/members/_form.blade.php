<form method="POST" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}">
    @csrf
    @if($member->exists) @method('PUT') @endif
    <div class="form-grid">
        <label>Full name<input name="full_name" value="{{ old('full_name', $member->full_name) }}" maxlength="150" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email', $member->email) }}" required></label>
        <label>Phone number <small class="muted">Optional</small><input name="phone" value="{{ old('phone', $member->phone) }}" maxlength="30"></label>
        <label>Membership category<select name="membership_category_id" required><option value="">Choose a category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('membership_category_id', $member->membership_category_id) == $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (inactive)' }}</option>@endforeach</select></label>
        <label>Membership level<select name="membership_level_id" required><option value="">Choose a level</option>@foreach($levels as $level)<option value="{{ $level->id }}" @selected(old('membership_level_id', $member->membership_level_id) == $level->id)>{{ $level->name }} · ₱{{ number_format((float)$level->fee, 2) }} / {{ $level->validity_months }} months{{ $level->is_active ? '' : ' (inactive)' }}</option>@endforeach</select></label>
    </div>
    @if($member->exists)<label class="check-row"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active))> Active member</label>@endif
    <div class="form-actions"><button type="submit">{{ $member->exists ? 'Save changes' : 'Register member' }}</button><a href="{{ $member->exists ? route('members.show', $member) : route('members.index') }}" role="button" class="outline secondary">Cancel</a></div>
</form>
