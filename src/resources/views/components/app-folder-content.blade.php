{{-- This occurs at the top of the page for a folder. --}}
@php
    $withPageNumbers = isset($options['pageNumbers']);
@endphp

@if (request()->input('format') !== 'pdf')
    <h1>
        <x-scribo::app-partwise-tag :tag="$nodeItem->findPartwiseOrder()" />{{ $nodeItem->metadata('title') }}
    </h1>
@endif

<x-scribo::app-contents-card :item="$nodeItem" :withPageNumbers="$withPageNumbers" :options="$options" />

{{--
@foreach ($nodeItem->getTree('rrr') as $item)
    <h2><x-scribo::app-partwise-tag :tag="$item->object->findPartwiseOrder()" />{{ $item->object->metadata('title') }}</h2>
@endforeach
--}}
