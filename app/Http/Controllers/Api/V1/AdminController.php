<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use App\Models\KycSubmission;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->hasRole('admin')) {
                return $this->error('Unauthorized. Admin access required.', 403);
            }
            return $next($request);
        });
    }

    /**
     * Get all users.
     */
    public function users(Request $request)
    {
        $users = User::with(['wallet', 'bellbankAccount'])
            ->paginate($request->get('per_page', 20));

        return $this->success($users, 'Users retrieved successfully');
    }

    /**
     * Get KYC submissions pending review.
     */
    public function kycSubmissions(Request $request)
    {
        $submissions = KycSubmission::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->paginate($request->get('per_page', 20));

        return $this->success($submissions, 'KYC submissions retrieved successfully');
    }

    /**
     * Review KYC submission.
     */
    public function reviewKyc(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $submission = KycSubmission::findOrFail($id);
        $submission->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $submission->user->update(['kyc_status' => $request->status]);

        return $this->success($submission, 'KYC reviewed successfully');
    }

    /**
     * Get transactions.
     */
    public function transactions(Request $request)
    {
        $transactions = Transaction::with(['walletFrom.user', 'walletTo.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return $this->success($transactions, 'Transactions retrieved successfully');
    }

    /**
     * Get system statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('kyc_status', 'approved')->count(),
            'total_transactions' => Transaction::count(),
            'total_volume' => Transaction::where('status', 'settled')->sum('amount'),
            'pending_kyc' => KycSubmission::where('status', 'pending')->count(),
        ];

        return $this->success($stats, 'Statistics retrieved successfully');
    }
}

