@extends('bookkeeping.v1.base')

@section('pagetitle', 'Top')

@section('content')
<div id="accountbook">
    <table>
        <tr>
            <td class="main">
                <table>
                    <tr>
                        <td colspan="2">
                            <b>{{{ $date }}}{{ __("'s state") }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <b>1. {{ __('Income statement') }}</b>
                            @include('bookkeeping.v1.incomestatement')
                        </td>
                        <td valign="top" class="leftspace">
                            <b>2. {{ __('Balance sheet') }}</b>
                            @include('bookkeeping.v1.balancesheet')
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                        @if (count($slips) != 0)
                            <b>3. {{ __('Journal') }}</b>
                            @include('bookkeeping.v1.slips')
                        @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
@endsection
