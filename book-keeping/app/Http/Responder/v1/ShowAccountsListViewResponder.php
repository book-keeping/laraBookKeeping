<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class ShowAccountsListViewResponder extends BaseViewResponder
{
    /**
     * Respond with the ShowAccountsListView.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $accounts = $context['accounts'];
        $account_list = $this->translateAccountsListFormat(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ]
        );
        $this->response->setContent($this->view->make('bookkeeping.v1.pageaccountslist', [
            'navilinks'      => $this->navilinks(),
            'accounts_list'  => $account_list,
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
