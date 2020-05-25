@extends('settings.settings')

@section('pagetitle', 'Personal access tokens')

@section('settings_content')
<div class="card">
    <div class="card-header">{{ __('Personal access tokens') }}</div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item" style="border: 0 none;">
                <div class="text-right">
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            {{ __('Generate new token') }}
                        </button>
                    </form>
                </div>
            </li>
        </ul>
        <br>
        @if ($message_for_new_token != null)
        <ul class="list-group">
            <li class="list-group-item border-info" style="background-color:#e3f2fd;">
                {{ $message_for_new_token }}
            </li>
        </ul>
        <br>
        @endif
        @if ($message_for_no_token != null)
        <ul class="list-group">
            <li class="list-group-item">
                {{ $message_for_no_token }}
            </li>
        </ul>
        @else
        <ul class="list-group">
            <li class="list-group-item">
                @if ($token != null)
                <div class="float-left">{{ $token }}</div>
                @else
                <div class="float-left">{{ $timestamp }}</div>
                @endif
                <div class="text-right">
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <input name="_method" type="hidden" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </li>
        </ul>
        @endif
    </div>
</div>
@endsection
