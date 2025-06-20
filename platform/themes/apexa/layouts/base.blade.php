<!doctype html>
<html {!! Theme::htmlAttributes() !!}>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Theme::partial('css-variable-declare') !!}

    {!! Theme::header() !!}
</head>

<body {!! Theme::bodyAttributes() !!} >
<a href="https://m.me/112990523740715" target="_blank" style="bottom: 100px; background:var(--tg-theme-primary);border:none;border-radius:20px;color:var(--tg-color-white-default);cursor:pointer;font-size:16px;height:45px;width:45px;position:fixed;right:50px;text-align:center;transition:1s ease;z-index:99999" data-target="html">
    <img src="https://skkntienganh.com/storage/skkntienganh/social-facebook-messenger-circle-512.png"/>
</a>
<a href="https://zalo.me/0986811223" target="_blank" style="bottom: 170px; background:var(--tg-theme-primary);border:none;border-radius:20px;color:var(--tg-color-white-default);cursor:pointer;font-size:16px;height:45px;width:45px;position:fixed;right:50px;text-align:center;transition:1s ease;z-index:99999" data-target="html">
    <img src="https://skkntienganh.com/storage/skkntienganh/zalo-icon-circle-1.png"/>
</a>
<a href="#" title="{{ __('Back to top') }}" class="scroll__top scroll-to-target" data-target="html">
    <x-core::icon name="ti ti-chevron-up"/>
</a>

{!! apply_filters(THEME_FRONT_BODY, null) !!}

{!! Theme::partial('header') !!}

<main class="fix">
    @yield('content')
</main>

<script>
    'use strict';

    window.siteConfig = {};
</script>

{!! Theme::partial('footer') !!}

{!! Theme::footer() !!}
</body>
</html>

