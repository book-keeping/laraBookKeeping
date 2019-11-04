<td class="tx">
@component('bookkeeping.v1.item', ['bold' => $item['debit']['bold'], 'italic' => $item['debit']['italic']])
    {{{ $item['debit']['title'] }}}
@endcomponent
</td>
<td class="debit">
@component('bookkeeping.v1.item', ['bold' => $item['debit']['bold'], 'italic' => $item['debit']['italic']])
    {{{ $item['debit']['amount'] }}}
@endcomponent
</td>
<td class="tx">
@component('bookkeeping.v1.item', ['bold' => $item['credit']['bold'], 'italic' => $item['credit']['italic']])
    {{{ $item['credit']['title'] }}}
@endcomponent
</td>
<td class="credit">
@component('bookkeeping.v1.item', ['bold' => $item['credit']['bold'], 'italic' => $item['credit']['italic']])
    {{{ $item['credit']['amount'] }}}
@endcomponent
</td>
