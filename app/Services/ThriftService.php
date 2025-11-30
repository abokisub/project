<?php

namespace App\Services;

use App\Models\ThriftGroup;
use App\Models\ThriftMember;
use App\Models\ThriftContribution;
use App\Models\ThriftPayout;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThriftService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Create a thrift group.
     */
    public function createGroup($organizerId, $data)
    {
        return DB::transaction(function () use ($organizerId, $data) {
            $group = ThriftGroup::create([
                'name' => $data['name'],
                'organizer_id' => $organizerId,
                'contribution_amount' => $data['contribution_amount'],
                'frequency' => $data['frequency'],
                'start_date' => $data['start_date'] ?? Carbon::today(),
                'status' => 'active',
            ]);

            // Add organizer as first member
            ThriftMember::create([
                'thrift_group_id' => $group->id,
                'user_id' => $organizerId,
                'position' => 1,
                'status' => 'active',
            ]);

            $group->increment('total_members');

            return $group;
        });
    }

    /**
     * Join a thrift group.
     */
    public function joinGroup($groupId, $userId)
    {
        return DB::transaction(function () use ($groupId, $userId) {
            $group = ThriftGroup::findOrFail($groupId);

            if ($group->status !== 'active') {
                throw new \Exception('Group is not accepting new members');
            }

            // Check if user is already a member
            $existingMember = ThriftMember::where('thrift_group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if ($existingMember) {
                throw new \Exception('User is already a member of this group');
            }

            $position = $group->total_members + 1;

            ThriftMember::create([
                'thrift_group_id' => $groupId,
                'user_id' => $userId,
                'position' => $position,
                'status' => 'active',
            ]);

            $group->increment('total_members');

            return $group;
        });
    }

    /**
     * Make a contribution.
     */
    public function contribute($groupId, $userId, $amount = null)
    {
        return DB::transaction(function () use ($groupId, $userId, $amount) {
            $group = ThriftGroup::findOrFail($groupId);
            $member = ThriftMember::where('thrift_group_id', $groupId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $contributionAmount = $amount ?? $group->contribution_amount;

            // Create contribution record
            $contribution = ThriftContribution::create([
                'thrift_group_id' => $groupId,
                'user_id' => $userId,
                'amount' => $contributionAmount,
                'due_date' => Carbon::today(),
                'status' => 'pending',
            ]);

            // Transfer from user wallet to group pool (simplified - in reality, this would be held in escrow)
            try {
                $transaction = $this->walletService->transfer(
                    $userId,
                    $group->organizer_id, // Simplified: organizer holds the pool
                    $contributionAmount
                );

                $contribution->update([
                    'transaction_id' => $transaction->id,
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                $member->increment('total_contributed', $contributionAmount);

                return $contribution;
            } catch (\Exception $e) {
                $contribution->update(['status' => 'missed']);
                throw $e;
            }
        });
    }

    /**
     * Process auto-debit for a group.
     */
    public function processAutoDebit($groupId)
    {
        $group = ThriftGroup::findOrFail($groupId);
        $members = ThriftMember::where('thrift_group_id', $groupId)
            ->where('status', 'active')
            ->get();

        foreach ($members as $member) {
            try {
                $this->contribute($groupId, $member->user_id);
            } catch (\Exception $e) {
                // Log failed contribution
                \Log::error("Auto-debit failed for user {$member->user_id} in group {$groupId}: " . $e->getMessage());
            }
        }
    }
}

