<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
    <head>
        <x-scribo::app-meta />

        <title>{{ config('app.name') }}</title>

        <x-scribo::app-styles />

        <x-scribo::app-scripts at="start" />
    </head>

    <body class="vh-100 d-flex flex-column">
        @if (request()->input('format') !== 'pdf')
            <header class="bg-info">
                <x-scribo::app-nav :binder="$nodeItem->getBinder()" />
            </header>
        @endif

        <div class="flex-grow-1 d-flex @if (request()->input('format') === 'html') overflow-y-hidden @endif">
            @if (request()->input('format') !== 'pdf')
                <x-scribo::app-sidebar-container :nodeItem="$nodeItem" />
            @endif

            <x-scribo::app-content-container :binder="$binder" :nodeItem="$nodeItem" :isBinder="$isBinder" :isContent="$isContent" :options="$options" :alert="$alert"/>
        </div>

        <x-scribo::app-scripts at="end" />
    </body>
</html>
