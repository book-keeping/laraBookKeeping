<table>
    <tr>
        <th class="tx">{{ __('Expense') }}</th>
        <th class="debit">{{ __('Amount') }}</th>
        <th class="tx">{{ __('Revenue') }}</th>
        <th class="credit">{{ __('Amount') }}</th>
    </tr>
    @foreach ($income_statement as $item)
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
