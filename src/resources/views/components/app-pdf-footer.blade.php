<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        {{-- <link href="/google-fonts.css" rel="stylesheet"> --}}

        <style>
            #header, #footer {
                padding: 0 !important;
            }
        </style>

        <style>
            :root {
                --bs-font-serif: "Source Serif 4";
                --bs-font-sans-serif: "Source Sans 3", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                --bs-font-monospace: "Source Code Pro", SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                --bs-body-font-family: var(--bs-font-serif);
                -webkit-print-color-adjust: exact;
            }

            .class-here {
                font-family: var(--bs-body-font-family);
                font-size: 16px;
                text-align: center;
                --bs-bg-opacity: 1;
                background-color: rgba(var(--bs-info-rgb), var(--bs-bg-opacity)) !important;
                width: 100vw;
                height: 100%;
            }

            body {
                height: 20mm;
            }

        </style>
    </head>

    <body>
        <div class="class-here" style="display: flex; flex-direction: column;">
            <div class="top-of-footer" style="flex-grow: 1"></div>
            <div class="middle-of-footer" style="flex-shrink: 0; height: 7mm;">@pageNumber</div>
            <div class="bottom-of-footer" style="flex-grow: 1"></div>
        </div>
    </body>
</html>
