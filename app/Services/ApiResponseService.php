<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

/**
 * Service for Marke
 */
class ApiResponseService
{
    /**
     *  Success API Response
     *
     * @return JsonResponse
     */
    public function apiSucccessResponse($data = null)
    {
        return response()
            ->json([
                'code' => 0,
                'message' => 'Success',
                'ResultData' => $data,
            ], 200);
    }

    /**
     *  Success API Response
     *
     * @return JsonResponse
     */
    public function apiFailedResponse($message)
    {
        return response()
            ->json([
                'code' => 1,
                'message' => $message,
            ], 500);
    }
}
