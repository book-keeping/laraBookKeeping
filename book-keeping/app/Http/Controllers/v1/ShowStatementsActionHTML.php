<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\ShowStatementsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowStatementsActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowStatementsView responder instance.
     *
     * @var \App\Http\Responder\v1\ShowStatementsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                    $BookKeeping
     * @param \App\Http\Responder\v1\ShowStatementsViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowStatementsViewResponder $responder)
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

        if ($request->isMethod('post')) {
            $beginning_date = trim($request->input('BEGINNING'));
            $end_date = trim($request->input('END'));
        } else {
            $beginning_date = $today;
            $end_date = $today;
        }
        $end_date_of_previous_period = date('Y-m-d', strtotime($beginning_date) - 86400);

        $context['beginning_date'] = $beginning_date;
        $context['end_date'] = $end_date;
        $context['statements'] = $this->BookKeeping->retrieveStatements($beginning_date, $end_date);
        $context['previous_balance_sheet'] = $this->BookKeeping->retrieveStatements('1970-01-01', $end_date_of_previous_period);
        $context['balance_sheet'] = $this->BookKeeping->retrieveStatements('1970-01-01', $end_date);
        $context['slips'] = $this->BookKeeping->retrieveSlips($beginning_date, $end_date);

        return $this->responder->response($context);
    }
}
