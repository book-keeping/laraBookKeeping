<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class FindSlipsViewResponder extends BaseViewResponder
{
    /**
     * Respond with the FindSlipsView.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $accounts = $context['accounts'];
        $account_title_list = $this->translateAccountListToTitleList(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ]
        );
        if (is_null($context['and_or'])) {
            $and_or = 'and';
        } else {
            $and_or = $context['and_or'];
        }
        $this->response->setContent($this->view->make('bookkeeping.v1.pagefindslips', [
            'navilinks'          => $this->navilinks(),
            'account_title_list' => $account_title_list,
            'beginning_date'     => $context['beginning_date'],
            'end_date'           => $context['end_date'],
            'debit'              => $context['debit'],
            'credit'             => $context['credit'],
            'and_or'             => $and_or,
            'keyword'            => $context['keyword'],
            'slips'              => $this->translateSlipsFormat($context['slips']),
            'modify'             => true,
            'message'            => $context['message'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
