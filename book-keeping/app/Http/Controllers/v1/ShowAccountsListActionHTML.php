<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\ShowAccountsListViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowAccountsListActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowAccountsListView responder instance.
     *
     * @var \App\Http\Responder\v1\ShowAccountsListViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                      $BookKeeping
     * @param \App\Http\Responder\v1\ShowAccountsListViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowAccountsListViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];
        $context['accounts'] = $this->BookKeeping->retrieveAccounts();

        return $this->responder->response($context);
    }
}
