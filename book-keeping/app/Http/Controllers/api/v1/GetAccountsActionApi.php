<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\AccountsJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAccountsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * AccountsJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\AccountsJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                  $BookKeeping
     * @param \App\Http\Responder\api\v1\AccountsJsonResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, AccountsJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $context = [];
        $context['accounts'] = $this->BookKeeping->retrieveAccountsList();

        return $this->responder->response($context);
    }
}
