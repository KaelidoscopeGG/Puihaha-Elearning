<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MembershipTransaction extends Model
{
    public const STATUSES = ['pending', 'paid', 'approved', 'expired', 'cancelled'];
    protected $fillable = ['member_id', 'membership_level_id', 'type', 'status', 'amount_due', 'starts_on', 'expires_on', 'created_by'];
    protected function casts(): array { return ['amount_due' => 'decimal:2', 'starts_on' => 'date', 'expires_on' => 'date']; }

    protected static function booted(): void
    {
        static::creating(function (MembershipTransaction $transaction) {
            if (!$transaction->reference_number) {
                do { $reference = 'TXN-'.now()->format('Ymd').'-'.Str::upper(Str::random(7)); }
                while (static::where('reference_number', $reference)->exists());
                $transaction->reference_number = $reference;
            }
        });
    }

    public function member(): BelongsTo { return $this->belongsTo(Member::class)->withTrashed(); }
    public function level(): BelongsTo { return $this->belongsTo(MembershipLevel::class, 'membership_level_id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
