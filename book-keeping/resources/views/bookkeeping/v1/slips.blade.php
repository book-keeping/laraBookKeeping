<table>
    <tr>
        @isset($modify)
        <th></th>
        @endisset
        <th class="tx">No.</th>
        <th class="tx">Slip No.</th>
        <th class="tx">{{ __('Date') }}</th>
        <th class="tx">{{ __('Debit') }}</th>
        <th class="tx">{{ __('Amount') }}</th>
        <th class="tx">{{ __('Credit') }}</th>
        <th class="tx">{{ __('Client') }}</th>
        <th>{{ __('Outline') }}</th>
    </tr>
    @foreach ($slips as $key => $item)
    <tr>
        @isset($modify)
        <td><input type="checkbox" name="modifyno[]" value="{{{ $key }}}" /></td>
        @endisset
        <td class="txc">{{{ $item['no'] }}}</td>
        <td class="txc">{{{ $item['slipno'] }}}</td>
        <td class="tx">{{{ $item['date'] }}}</td>
        <td class="tx">{{{ $item['debit'] }}}</td>
        <td class="txn">{{{ $item['amount'] }}}</td>
        <td class="tx">{{{ $item['credit'] }}}</td>
        <td class="tx">{{{ $item['client'] }}}</td>
        <td>{{{ $item['outline'] }}}</td>
    </tr>
    @endforeach
    <tr>
        @isset($modify)
        <td class="footer"></td>
        @endisset
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
        <td class="footer"></td>
    </tr>
</table>
