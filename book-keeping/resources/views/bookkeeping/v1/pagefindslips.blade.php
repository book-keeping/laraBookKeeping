@extends('bookkeeping.v1.base')

@section('pagetitle', 'Find Slips')

@section('content')
<div id="accountbook">
    <table>
        <form method="POST" action="{{ route('v1_findslips') }}">
        @csrf
            <tr>
                <td class="top">
                    <table>
                        <tr>
                            <td class="intop">
                                {{ __('From') }} <input value="{{ $beginning_date }}" size="14" name="BEGINNING" type="text" />&nbsp;&nbsp;
                                {{ __('To') }} <input value="{{ $end_date }}" size="14" name="END" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="intop">
                                {{ __('Debit') }} <select name="debit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title)
                                    @if ($debit == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif
                                    @endforeach
                                </select>&nbsp;&nbsp;
                                [
                                @if ($and_or == 'and')
                                <input name="ANDOR" value="and" type="radio" checked/>
                                @else
                                <input name="ANDOR" value="and" type="radio" />
                                @endif
                                and /
                                @if ($and_or == 'or')
                                <input name="ANDOR" value="or"  type="radio" checked/>
                                @else
                                <input name="ANDOR" value="or"  type="radio" />
                                @endif
                                or
                                ]&nbsp;&nbsp;
                                {{ __('Credit') }} <select name="credit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title)
                                    @if ($credit == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="intop">
                                {{ __('Keyword') }} <input value="{{ $keyword }}" size="24" name="KEYWORD" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="intop">
                                <input name="buttons[search]" value="{{ __('Search') }}" type="submit" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="main">
                    @if (count($slips) != 0)
                        @include('bookkeeping.v1.slips')
                        <input name="buttons[delete]" value="{{ __('Delete') }}" type="submit" />
                    @endif
                    @isset($message)
                        {{ $message }}
                    @endisset
                </td>
            </tr>
        </form>
    </table>
</div>
@endsection
