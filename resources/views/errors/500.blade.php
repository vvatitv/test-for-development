@extends('layouts.error')
@section('title', __('Техническое обслуживание'))
@section('content')
<div class="main-body-container --error-page">
    <div class="error-code">Техническое обслуживание</div>
    {{-- <div class="error-text">Техническое обслуживание</div> --}}
    <div class="error-description">
        <p>Нам очень жаль что вы попали сюда, пожалуйста обновите страницу, возможно, мы уже закончили.</p>
    </div>
    <div class="error-actions">
        <a href="{{ route('home') }}">
            <span class="text">На главную</span>
        </a>
    </div>
</div>
@endsection
