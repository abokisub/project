<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use ApiResponse;

    /**
     * Handle BellBank webhook.
     */
    public function bellbank(Request $request)
    {
        // TODO: Implement HMAC signature verification
        // TODO: Process webhook data
        // TODO: Update transaction statuses
        
        return $this->success(null, 'Webhook received');
    }
}

