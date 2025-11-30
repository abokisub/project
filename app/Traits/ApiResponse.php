<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'code' => $code,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}

