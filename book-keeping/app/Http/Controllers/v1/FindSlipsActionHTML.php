<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\FindSlipsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FindSlipsActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * FindSlipsViewResponder responder instance.
     *
     * @var \App\Http\Responder\v1\FindSlipsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService               $BookKeeping
     * @param \App\Http\Responder\v1\FindSlipsViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, FindSlipsViewResponder $responder)
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
        $beginning_date = null;
        $end_date = null;
        $debit = null;
        $credit = null;
        $and_or = null;
        $keyword = null;
        $slips = [];
        $message = __('There is no condition for search.');

        $context['accounts'] = $this->BookKeeping->retrieveAccounts(true);
        if ($request->isMethod('post')) {
            $button_action = key($request->input('buttons'));
            $modifyno = $request->input('modifyno');
            if (($button_action == 'delete') && (!empty($modifyno))) {
                foreach ($modifyno as $slipEntryId) {
                    $this->BookKeeping->deleteSlipEntryAsDraft($slipEntryId);
                }
            }
            $beginning_date = trim($request->input('BEGINNING'));
            $end_date = trim($request->input('END'));
            $debit = $request->input('debit');
            $credit = $request->input('credit');
            $and_or = $request->input('ANDOR');
            $keyword = trim($request->input('KEYWORD'));
            if (!empty($beginning_date) || !empty($end_date) || !empty($debit) || !empty($credit) || !empty($keyword)) {
                if ($this->BookKeeping->validatePeriod($beginning_date, $end_date)) {
                    $slips = $this->BookKeeping->retrieveSlips($beginning_date, $end_date, $debit, $credit, $and_or, $keyword);
                    $message = null;
                    if (empty($slips)) {
                        $message = __('No items that match the condition.');
                    }
                } else {
                    $message = __('Invalid date format.');
                }
            }
        }
        $context['beginning_date'] = $beginning_date;
        $context['end_date'] = $end_date;
        $context['debit'] = $debit;
        $context['credit'] = $credit;
        $context['and_or'] = $and_or;
        $context['keyword'] = $keyword;
        $context['slips'] = $slips;
        $context['message'] = $message;

        return $this->responder->response($context);
    }
}
