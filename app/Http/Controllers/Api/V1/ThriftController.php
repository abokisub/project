<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\ThriftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThriftController extends Controller
{
    use ApiResponse;

    protected $thriftService;

    public function __construct(ThriftService $thriftService)
    {
        $this->thriftService = $thriftService;
    }

    /**
     * Create a thrift group.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contribution_amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:daily,weekly,monthly',
            'start_date' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $group = $this->thriftService->createGroup($request->user()->id, $request->all());
            return $this->success($group, 'Thrift group created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Join a thrift group.
     */
    public function join(Request $request, $id)
    {
        try {
            $group = $this->thriftService->joinGroup($id, $request->user()->id);
            return $this->success($group, 'Joined thrift group successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Make a contribution.
     */
    public function contribute(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $contribution = $this->thriftService->contribute(
                $id,
                $request->user()->id,
                $request->amount
            );
            return $this->success($contribution, 'Contribution made successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get thrift group details.
     */
    public function show($id)
    {
        $group = \App\Models\ThriftGroup::with(['members', 'contributions'])
            ->findOrFail($id);

        return $this->success($group, 'Thrift group retrieved successfully');
    }
}

