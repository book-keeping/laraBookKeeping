@extends('bookkeeping.v1.base')

@section('pagetitle', 'Accounts List')

@section('content')
<div id="accounts">
    <table>
        <tr>
            <td class="main">
                <table>
                    <tr>
                        <th class="inmain">{{ __('Code') }}</th>
                        <th class="inmain">{{ __('Type') }}</th>
                        <th class="inmain">{{ __('Account Group Title') }}</th>
                        <th class="inmain">{{ __('Account Title') }}</th>
                        <th class="inmain">{{ __('Description') }}</th>
                    </tr>
                    @foreach ($accounts_list as $accountKey => $account)
                    <tr class="{{ $account['trclass'] }}">
                        <td class="inmain">{{ $account['code'] }}</td>
                        <td class="inmain">{{ $account['type'] }}</td>
                        <td class="inmain">{{ $account['group_title'] }}</td>
                        <td class="inmain">{{ $account['title'] }}</td>
                        <td class="inmain">{{ $account['description'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
</div>
@endsection
