@extends('bookkeeping.v1.base')

@section('pagetitle', 'Slip')

@section('content')
<div id="accounts">
    <table>
        <tr>
            <td class="top">
                <table>
                    <form method="POST" action="{{ route('v1_slip') }}">
                    @csrf
                        <tr>
                            <td class="intop">{{ __('Debit') }}</td>
                            <td class="intop">{{ __('Client') }}</td>
                            <td class="intop">{{ __('Outline') }}</td>
                            <td class="intop">{{ __('Credit') }}</td>
                            <td class="intop">{{ __('Amount') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="intop">
                                <select name="debit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title)
                                    @if ($add['debit'] == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td class="intop"><input value="{{ $add['client'] }}" size="15" name="client" type="text" /></td>
                            <td class="intop"><input value="{{ $add['outline'] }}" size="40" name="outline" type="text" /></td>
                            <td class="intop">
                                <select name="credit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title)
                                    @if ($add['credit'] == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td class="intop"><input value="{{ $add['amount'] }}" size="15" name="amount" type="text" /></td>
                            <td><input name="buttons[add]" value="{{ __('Add') }}" type="submit" /></td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr><td><!-- This line shows white separater --></td></tr>
        <tr>
            <td class="top">
                <!-- Multiple Add is not supported already -->
            </td>
        </tr>
        <tr>
            <td class="main">
                @empty($draftslip)
                {{ __('There is no item.') }}
                @else
                <form method="POST" action="{{ route('v1_slip') }}">
                @csrf
                    <table >
                        <tr>
                            <td colspan="7">
                                Date: <input value="{{ $slipdate }}" size="14" name="date" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <th class="inmain">{{ __('No.') }}</th>
                            <th class="inmain">{{ __('Debit') }}</th>
                            <th class="inmain">{{ __('Client') }}</th>
                            <th class="inmain">{{ __('Outline') }}</th>
                            <th class="inmain">{{ __('Credit') }}</th>
                            <th class="inmain">{{ __('Amount') }}</th>
                            <th class="inmain"></th>
                        </tr>
                        @foreach ($draftslip as $draftslipKey => $draftslipItem)
                        <tr class="{{ $draftslipItem['trclass'] }}">
                            <td class="inmain" style="font-family: Consolas, 'Courier New', Courier, Monaco, monospace;">{{ $draftslipItem['no'] }}</td>
                            <td class="inmain">{{ $draftslipItem['debit'] }}</td>
                            <td class="inmain">{{ $draftslipItem['client'] }}</td>
                            <td class="inmain">{{ $draftslipItem['outline'] }}</td>
                            <td class="inmain">{{ $draftslipItem['credit'] }}</td>
                            <td class="inmain" align="right">{{ $draftslipItem['amount'] }}</td>
                            <td class="inmain">
                                <input type="radio" name="modifyno" value="{{ $draftslipKey }}" />
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="inmain" colspan="5" align="right">{{ __('Total') }}</td>
                            <td class="inmain" align="right">{{ $totalamount }}</td>
                            <td><input name="buttons[delete]" value="{{ __('Delete') }}" type="submit" /></td>
                        </tr>
                    </table>
                    <input name="buttons[submit]" value="OK" type="submit" />
                </form>
                @endempty
            </td>
        </tr>
    </table>
</div>
@endsection
