<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\MembershipTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireMembershipTransactions extends Command
{
    protected $signature = 'memberships:expire';
    protected $description = 'Mark ended approved memberships as expired and deactivate members with no current membership';

    public function handle(): int
    {
        $expired = MembershipTransaction::where('status', 'approved')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', today())
            ->get(['id', 'member_id']);

        if ($expired->isEmpty()) {
            $this->info('No memberships need to expire.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($expired) {
            MembershipTransaction::whereIn('id', $expired->pluck('id'))->update(['status' => 'expired']);
            $memberIds = $expired->pluck('member_id')->unique();
            Member::whereIn('id', $memberIds)
                ->whereDoesntHave('transactions', fn ($query) => $query->where('status', 'approved')->whereDate('expires_on', '>=', today()))
                ->update(['is_active' => false]);
        });

        $this->info($expired->count().' membership transaction(s) marked expired.');
        return self::SUCCESS;
    }
}
