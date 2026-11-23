<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = ['membership_transaction_id', 'amount', 'method', 'paid_at', 'recorded_by', 'notes'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_at' => 'datetime']; }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (!$payment->payment_reference) {
                do { $reference = 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(7)); }
                while (static::where('payment_reference', $reference)->exists());
                $payment->payment_reference = $reference;
            }
        });
    }

    public function transaction(): BelongsTo { return $this->belongsTo(MembershipTransaction::class, 'membership_transaction_id'); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
