<table>
    <tr>
        <th class="tx">{{ __('Assets') }}</th>
        <th class="debit">{{ __('Amount') }}</th>
        <th class="tx">{{ __('Liabilities') }}</th>
        <th class="credit">{{ __('Amount') }}</th>
    </tr>
    @foreach ($trial_balance_of_real_flow as $item)
    <tr>
        @include('bookkeeping.v1.debitcreditline')
    </tr>
    @endforeach
    <tr>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
    </tr>
</table>
