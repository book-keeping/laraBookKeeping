@extends('layouts.bookkeeping')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-2 p-2">
            <ul class="nav flex-column bg-white border-top border-left border-right">
                @foreach ($settingnavilinks as $navi)
                <li class="nav-item border-bottom">
                    @if ($navi['link'] == $selflinkname)
                    <span class="nav-link" style="border-left: 2px solid #e36209;">{{{ $navi['caption'] }}}</span>
                    @else
                    <a class="nav-link" href="{{ route( $navi['link'] ) }}" style="border-left: 2px solid #ffffff;">{{{ $navi['caption'] }}}</a>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-12 col-lg-6 p-2">
            @yield('settings_content')
        </div>
    </div>
</div>
@endsection
