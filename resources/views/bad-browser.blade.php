@extends('layouts.error')
@section('title', __('Ваш браузер устарел'))
@section('content')
<div class="main-body-container --error-page">
    <div class="error-code">Ваш браузер устарел</div>
    <div class="error-text">Наш сайт может работать медленно и с ошибками. Для быстрой и стабильной работы рекомендуем установить последнюю версию одного из этих браузеров:</div>
    <div class="error-description">
        <ul class="browser-lists">
            <li>
                <a href="https://www.google.com/chrome/" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-chrome.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Chrome</span>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/ru/" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-firefox.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Firefox</span>
                </a>
            </li>
            <li>
                <a href="https://browser.yandex.ru/" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-yandex.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Yandex</span>
                </a>
            </li>
            <li>
                <a href="https://www.opera.com/ru" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-opera.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Opera</span>
                </a>
            </li>
            <li>
                <a href="https://browser.ru/" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-atom.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Atom</span>
                </a>
            </li>
            <li>
                <a href="https://www.microsoft.com/ru-ru/edge" target="_blank" rel="nofollow">
                    <span class="icon">
                        <img src="{{ asset('storage/images/icon-browser-edge.svg') }}" alt="" class="img-fluid">
                    </span>
                    <span class="text">Edge</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="error-actions">
        <a href="{{ route('home') }}">
            <span class="text">На главную</span>
        </a>
    </div>
</div>
@endsection