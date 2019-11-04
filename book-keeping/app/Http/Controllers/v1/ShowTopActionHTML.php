<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\ShowTopViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowTopActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowTopView responder instance.
     *
     * @var \App\Http\Responder\v1\ShowTopViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService             $BookKeeping
     * @param \App\Http\Responder\v1\ShowTopViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowTopViewResponder $responder)
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
        $today = date('Y-m-d');
        $context['date'] = $today;
        $context['income_statement'] = $this->BookKeeping->retrieveStatements($today, $today);
        $context['balance_sheet'] = $this->BookKeeping->retrieveStatements('1970-01-01', $today);
        $context['slips'] = $this->BookKeeping->retrieveSlips($today, $today);

        return $this->responder->response($context);
    }
}
