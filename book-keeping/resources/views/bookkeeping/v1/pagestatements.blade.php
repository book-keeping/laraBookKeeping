@extends('bookkeeping.v1.base')

@section('pagetitle', 'Statements')

@section('content')
<div id="accountbook">
    <table>
        <tr>
            <td class="top">
                <table>
                <form method="POST" action="{{ route('v1_statements') }}">
                @csrf
                    <tr>
                        <td class="intop">
                            {{ __('From') }} <input value="{{{ $beginning_date }}}" size="14" name="BEGINNING" type="text" /> &nbsp;&nbsp;
                            {{ __('To') }} <input value="{{{ $end_date }}}" size="14" name="END" type="text" />
                            <input name="buttons[OK]" value="OK" type="submit" />
                        </td>
                    </tr>
                </form>
                </table>
            </td>
        </tr>
        <tr>
            <td class="main">
                @if ($display_statements)
                <table>
                    <tr>
                        <td colspan="2">
                            <b>{{ __('Accounting period') }} &nbsp;&nbsp;{{ __('From') }}: {{{ $beginning_date }}} &nbsp;&nbsp;{{ __('To') }}: {{{ $end_date }}}</b>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <b>1. {{ __('Income statement') }}</b>
                            @include('bookkeeping.v1.incomestatement')
                        </td>
                        <td valign="top" class="leftspace">
                            <b>2. {{ __('Trial Balance Of Real Flow') }}</b>
                            @include('bookkeeping.v1.trialbalancesheet')
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <b>3. {{ __('Previous Period-end Balance sheet') }}</b>
                            @include('bookkeeping.v1.previousbalancesheet')
                        </td>
                        <td valign="top" class="leftspace">
                            <b>4. {{ __('Period-end Balance sheet') }}</b>
                            @include('bookkeeping.v1.balancesheet')
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                        @if (count($slips) != 0)
                            <b>5. {{ __('Journal') }}</b>
                            @include('bookkeeping.v1.slips')
                        @endif
                        </td>
                    </tr>
                </table>
                @endif
                @isset($message)
                    {{ $message }}
                @endisset
            </td>
        </tr>
    </table>
</div>
@endsection
