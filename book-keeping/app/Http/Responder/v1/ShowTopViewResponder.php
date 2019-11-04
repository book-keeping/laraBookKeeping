<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class ShowTopViewResponder extends BaseViewResponder
{
    /**
     * Response the Books list and Form to create new Book.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $incomeStatement = $context['income_statement'];
        $balanceSheet = $context['balance_sheet'];
        $slips = $context['slips'];
        $this->response->setContent($this->view->make('bookkeeping.v1.pagetop', [
            'navilinks'        => $this->navilinks(),
            'date'             => $context['date'],
            'income_statement' => $this->translateIncomeStatementFormat([
                'expense' => [
                    'amount' => $incomeStatement['expense']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($incomeStatement['expense']['groups']),
                ],
                'revenue' => [
                    'amount' => $incomeStatement['revenue']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($incomeStatement['revenue']['groups']),
                ],
                'net_income' => $incomeStatement['net_income'],
            ]),
            'balance_sheet' => $this->translateBalanceSheetFormat([
                'asset' => [
                    'amount' => $balanceSheet['asset']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['asset']['groups']),
                ],
                'liability' => [
                    'amount' => $balanceSheet['liability']['amount'],
                    'groups' => $this->sortAccountInAscendingCodeOrder($balanceSheet['liability']['groups']),
                ],
                'current_net_asset' => $balanceSheet['current_net_asset'],
                'net_asset'         => $balanceSheet['net_asset'],
            ]),
            'slips' => $this->translateSlipsFormat($slips),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
