@extends('layouts.error')
@section('title', __('Страница не найдена'))
@section('content')
<div class="main-body-container --error-page">
    <div class="error-code">404</div>
    <div class="error-text">Упс! Что-то пошло не так</div>
    <div class="error-description">
        <p>Извините, запрошенная вами страница не найдена</p>
    </div>
    <div class="error-actions">
        <a href="{{ route('home') }}">
            <span class="text">На главную</span>
        </a>
    </div>
</div>
@endsection
