<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class CreateSlipViewResponder extends BaseViewResponder
{
    /**
     * Response the Form to create new Slip.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $addparameter = ['debit' => '', 'client' => '', 'outline' => '', 'credit' => '', 'amount' => ''];
        if (array_key_exists('add', $context)) {
            $addparameter = $context['add'];
        }

        $accounts = $context['accounts'];
        $account_title_list = $this->translateAccountListFormat(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ]
        );
        $draftslip = [];
        if (!empty($context['draftslip'])) {
            $draftslip = $this->translateDraftSlipFormat($context['draftslip']);
        }
        $this->response->setContent($this->view->make('bookkeeping.v1.pageslip', [
            'navilinks'          => $this->navilinks(),
            'add'                => $addparameter,
            'account_title_list' => $account_title_list,
            'slipdate'           => $context['slipdate'],
            'draftslip'          => $draftslip,
            'totalamount'        => $context['totalamount'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
