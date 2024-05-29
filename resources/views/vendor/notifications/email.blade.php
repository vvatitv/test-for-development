@component('mail::message')
@if( !empty($introLines) )
<table class="message-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tbody>
<tr>
<td>
<table class="message-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
<tbody>
<tr>
<td class="message-main-table-td column" width="100%">
<table class="message-main-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td class="message-main-table-td-table-td">
@foreach ($introLines as $line)
{!! $line !!}
@endforeach
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
@endif

@isset($actionText)
@component('mail::button', ['url' => $actionUrl, 'actionText' => $actionText])
{!! $actionText !!}
@endcomponent
@endisset

@if( !empty($outroLines) )
<table class="message-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tbody>
<tr>
<td>
<table class="message-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
<tbody>
<tr>
<td class="message-main-table-td column" width="100%">
<table class="message-main-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td class="message-main-table-td-table-td">
@foreach ($outroLines as $line)
{!! $line !!}
@endforeach
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
@endif

@isset($actionText)
@slot('subcopy')
@lang('Если кнопка «:actionText» по каким-либо причинам не работает, скопируйте и вставьте следующую ссылку в адресную строку браузера:', ['actionText' => $actionText]) <div>[{{ $displayableActionUrl }}]({{ $actionUrl }})</div>
@endslot
@endisset

@endcomponent