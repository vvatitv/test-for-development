@component('mail::layout')
{!! $slot !!}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

@slot('footer')
@component('mail::footer')
@endcomponent
@endslot

@endcomponent
