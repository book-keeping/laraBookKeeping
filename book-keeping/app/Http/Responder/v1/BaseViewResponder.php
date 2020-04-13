<?php

namespace App\Http\Responder\v1;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class BaseViewResponder
{
    /**
     * Response instance.
     *
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * View Factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new BaseV1ViewResponder instance.
     *
     * @param \Illuminate\Http\Response          $response
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * List of Navigation links for version 1.
     *
     * @return array
     */
    public function navilinks(): array
    {
        return [
            ['link' => 'v1_top', 'caption' => __('Top')],
            ['link' => 'v1_slip', 'caption' => __('Slip')],
            ['link' => 'v1_statements', 'caption' => __('Statements')],
        ];
    }

    /**
     * Sort accounts in ascending order of account code for version 1.
     *
     * @param array $groupedList
     *
     * @return array
     */
    public function sortAccountInAscendingCodeOrder(array $groupedList): array
    {
        return $this->sortAccountGrouptListInAscendingCodeOrder($groupedList);
    }

    /**
     * Translate account list format for view.
     *
     * @param array $accounts
     *
     * @return array
     */
    public function translateAccountsListFormat(array $accounts): array
    {
        $account_list = [];
        $accountTypeTitle = [
            'asset'     => __('Assets'),
            'liability' => __('Liabilities'),
            'expense'   => __('Expense'),
            'revenue'   => __('Revenue'),
        ];
        $trclass = 'evn';
        foreach ($accounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupItem) {
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    $account_list[$accountId] = [
                        'code'        => is_null($accountItem['bk_code']) ? '-' : $accountItem['bk_code'],
                        'type'        => $accountTypeTitle[$accountTypeKey],
                        'group_title' => $accountGroupItem['title'],
                        'title'       => $accountItem['title'],
                        'description' => $accountItem['description'],
                        'trclass'     => $trclass,
                    ];
                    if ($trclass == 'evn') {
                        $trclass = 'odd';
                    } else {
                        $trclass = 'evn';
                    }
                }
            }
        }

        return $account_list;
    }

    /**
     * Translate account list to title list for view.
     *
     * @param array $accounts
     *
     * @return array
     */
    public function translateAccountListToTitleList(array $accounts): array
    {
        $account_title_list = [];

        foreach ($accounts as $accountType) {
            foreach ($accountType['groups'] as $accountGroupItem) {
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    $account_title_list[$accountId] = $accountItem['title'];
                }
            }
        }

        return $account_title_list;
    }

    /**
     * Translate balance sheet format for view.
     *
     * @param array $statements
     *
     * @return array
     */
    public function translateBalanceSheetFormat(array $statements): array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle'             => 'Assets',
            'debitGroup'             => 'asset',
            'creditTitle'            => 'Liabilities',
            'creditGroup'            => 'liability',
            'displayCurrentNetAsset' => true,
        ]);
    }

    /**
     * Translate draft slips format for view.
     *
     * @param array $slips
     *
     * @return array
     */
    public function translateDraftSlipFormat(array $slip): array
    {
        $formatted = [];
        $slipId = key($slip);
        $trclass = 'evn';
        foreach ($slip[$slipId]['items'] as $slipEntryId => $slipEntryItem) {
            $formatted[$slipEntryId] = [
                'no'      => substr($slipEntryId, 0, 6).'..',
                'debit'   => $slipEntryItem['debit']['account_title'],
                'client'  => $slipEntryItem['client'],
                'outline' => $slipEntryItem['outline'],
                'credit'  => $slipEntryItem['credit']['account_title'],
                'amount'  => $slipEntryItem['amount'],
                'trclass' => $trclass,
            ];
            if ($trclass == 'evn') {
                $trclass = 'odd';
            } else {
                $trclass = 'evn';
            }
        }

        return $formatted;
    }

    /**
     * Translate income statement format for view.
     *
     * @param array $statements
     *
     * @return array
     */
    public function translateIncomeStatementFormat(array $statements): array
    {
        return $this->translateStatementsFormat($statements, [
            'debitTitle'             => 'Expense',
            'debitGroup'             => 'expense',
            'creditTitle'            => 'Revenue',
            'creditGroup'            => 'revenue',
            'displayCurrentNetAsset' => false,
        ]);
    }

    /**
     * Translate slips format for view.
     *
     * @param array $skips
     *
     * @return array
     */
    public function translateSlipsFormat(array $slips): array
    {
        $slipentryline = [];
        foreach ($slips as $slipId => $slip) {
            foreach ($slip['items'] as $slipEntryId => $slipEntry) {
                $slipentryline[] = [
                    'no'      => substr($slipEntryId, 0, 6).'..',
                    'slipno'  => substr($slipId, 0, 6).'..',
                    'date'    => $slip['date'],
                    'debit'   => $slipEntry['debit']['account_title'],
                    'credit'  => $slipEntry['credit']['account_title'],
                    'amount'  => number_format($slipEntry['amount']),
                    'client'  => $slipEntry['client'],
                    'outline' => $slipEntry['outline'],
                ];
            }
        }

        return $slipentryline;
    }

    /**
     * Get associative array which has account ID as key sorted in ascending order of value that is specified keyword.
     *
     * @param array $listWithKeyword
     *
     * @return array
     */
    private function getIdsSortedInAscendingOrder(array $listWithKeyword): array
    {
        $sortedIds_isCurrent_withCode = [];
        $sortedIds_isCurrent_withoutCode = [];
        $sortedIds_isNotCurrent_withCode = [];
        $sortedIds_isNotCurrent_withoutCode = [];
        foreach ($listWithKeyword as $Ids => $item) {
            if (array_key_exists('isCurrent', $item) && ($item['isCurrent'] == true)) {
                if (!is_null($item['bk_code'])) {
                    $sortedIds_isCurrent_withCode[$Ids] = $item['bk_code'];
                } else {
                    $sortedIds_isCurrent_withoutCode[$Ids] = $item['createdAt'];
                }
            } else {
                if (!is_null($item['bk_code'])) {
                    $sortedIds_isNotCurrent_withCode[$Ids] = $item['bk_code'];
                } else {
                    $sortedIds_isNotCurrent_withoutCode[$Ids] = $item['createdAt'];
                }
            }
        }
        asort($sortedIds_isCurrent_withCode);
        asort($sortedIds_isCurrent_withoutCode);
        asort($sortedIds_isNotCurrent_withCode);
        asort($sortedIds_isNotCurrent_withoutCode);

        return $sortedIds_isCurrent_withCode + $sortedIds_isCurrent_withoutCode + $sortedIds_isNotCurrent_withCode + $sortedIds_isNotCurrent_withoutCode;
    }

    /**
     * Sort account group list in ascending order of account code for version 1.
     *
     * @param array $groupedList
     *
     * @return array
     */
    private function sortAccountGrouptListInAscendingCodeOrder(array $groupedList): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($groupedList);
        foreach ($sortedKeys as $groupId => $keyword) {
            $reordered[$groupId] = $groupedList[$groupId];
            $reordered[$groupId]['items'] = $this->sortAccountListInAscendingCodeOrder($groupedList[$groupId]['items']);
        }

        return $reordered;
    }

    /**
     * Sort account list in ascending order of account code for version 1.
     *
     * @param array $groupedList
     *
     * @return array
     */
    private function sortAccountListInAscendingCodeOrder(array $list): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($list);
        foreach ($sortedKeys as $id => $keyword) {
            $reordered[$id] = $list[$id];
        }

        return $reordered;
    }

    /**
     * Translate statements format for view.
     *
     * @param array $statements
     *
     * @return array
     */
    private function translateStatementsFormat(array $statements, array $parameters): array
    {
        $debitTitle = $parameters['debitTitle'];
        $debitGroup = $parameters['debitGroup'];
        $creditTitle = $parameters['creditTitle'];
        $creditGroup = $parameters['creditGroup'];
        $displayCurrentNetAsset = $parameters['displayCurrentNetAsset'];

        $debitcreditline = [];
        $debit_count = 0;
        $credit_count = 0;

        $debitcreditline[$debit_count++]['debit'] = [
            'title'  => __($debitTitle),
            'amount' => number_format($statements[$debitGroup]['amount']),
            'bold'   => true,
            'italic' => true,
        ];
        foreach ($statements[$debitGroup]['groups'] as $key => $group) {
            $debitcreditline[$debit_count++]['debit'] = [
                'title'  => $group['title'],
                'amount' => number_format($group['amount']),
                'bold'   => false,
                'italic' => true,
            ];
            foreach ($group['items'] as $key => $item) {
                $debitcreditline[$debit_count++]['debit'] = [
                    'title'  => $item['title'],
                    'amount' => number_format($item['amount']),
                    'bold'   => false,
                    'italic' => false,
                ];
            }
        }
        if ($debitTitle == 'Expense') {
            $debitcreditline[$debit_count++]['debit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            $debitcreditline[$debit_count++]['debit'] = [
                'title'  => __('Net Income'),
                'amount' => number_format($statements['net_income']['amount']),
                'bold'   => true,
                'italic' => true,
            ];
        }

        $debitcreditline[$credit_count++]['credit'] = [
            'title'  => __($creditTitle),
            'amount' => number_format($statements[$creditGroup]['amount']),
            'bold'   => true,
            'italic' => true,
        ];
        foreach ($statements[$creditGroup]['groups'] as $key => $group) {
            $debitcreditline[$credit_count++]['credit'] = [
                'title'  => $group['title'],
                'amount' => number_format($group['amount']),
                'bold'   => false,
                'italic' => true,
            ];
            foreach ($group['items'] as $key => $item) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title'  => $item['title'],
                    'amount' => number_format($item['amount']),
                    'bold'   => false,
                    'italic' => false,
                ];
            }
        }
        if ($creditTitle == 'Liabilities') {
            $debitcreditline[$credit_count++]['credit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            if ($displayCurrentNetAsset && array_key_exists('current_net_asset', $statements)) {
                $debitcreditline[$credit_count++]['credit'] = [
                    'title'  => __('Current Net Asset'),
                    'amount' => number_format($statements['current_net_asset']['amount']),
                    'bold'   => true,
                    'italic' => true,
                ];
            }
            $debitcreditline[$credit_count++]['credit'] = [
                'title'  => __('Net Asset'),
                'amount' => number_format($statements['net_asset']['amount']),
                'bold'   => true,
                'italic' => true,
            ];
        }

        while ($debit_count != $credit_count) {
            if ($debit_count < $credit_count) {
                $debitcreditline[$debit_count++]['debit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            } else {
                $debitcreditline[$credit_count++]['credit'] = ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false];
            }
        }

        return $debitcreditline;
    }
}
