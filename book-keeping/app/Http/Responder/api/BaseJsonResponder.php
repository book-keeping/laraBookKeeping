<?php

namespace App\Http\Responder\api;

use Illuminate\Http\JsonResponse;

class BaseJsonResponder
{
    /**
     * JsonResponse instance.
     *
     * @var \Illuminate\Http\JsonResponse
     */
    protected $response;

    /**
     * Create a new BaseJsonResponder instance.
     *
     * @param \Illuminate\Http\JsonResponse $response
     */
    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
    }
}
