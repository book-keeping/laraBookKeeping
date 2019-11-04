@if ($bold)
<b>
@endif
@if ($italic)
<i>
@endif
    {{ $slot }}
@if ($italic)
</i>
@endif
@if ($bold)
</b>
@endif
