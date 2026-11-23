<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = ['full_name', 'email', 'phone', 'membership_category_id', 'membership_level_id', 'joined_on', 'is_active'];
    protected function casts(): array { return ['joined_on' => 'date', 'is_active' => 'boolean']; }

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (!$member->membership_number) {
                do { $number = 'MBR-'.now()->format('Y').'-'.Str::upper(Str::random(6)); }
                while (static::withTrashed()->where('membership_number', $number)->exists());
                $member->membership_number = $number;
            }
        });
    }

    public function category(): BelongsTo { return $this->belongsTo(MembershipCategory::class, 'membership_category_id'); }
    public function level(): BelongsTo { return $this->belongsTo(MembershipLevel::class, 'membership_level_id'); }
    public function transactions(): HasMany { return $this->hasMany(MembershipTransaction::class); }
}
