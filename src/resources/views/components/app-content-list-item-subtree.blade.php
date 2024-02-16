THIS SHOULD NEVER HAPPEN

@php
    $subtree = $item->object->getTree($globalOrder ?? $item->global_order);

    $divClass = $item->depth === 1 && count($subtree) === 0 ? 'pb-2' : '';
@endphp

@if ($displayItem ?? true)
    <div class="{{ $divClass }}">
    <x-scribo::app-partwise-tag :tag="$nodeItem->findPartwiseOrder()" /><a href="{{ $item->object->href() }}">{{ $item->object->metadata('title') }}</a>
    </div>
@endif

@foreach ($subtree as $subItem)
    @if ($loop->first)
        <ul class="list-group pb-2">
    @endif

    <li class="list-group-item py-0 border border-0">
        <x-scribo::app-content-list-item-subtree :item="$subItem" />
    </li>

    @if ($loop->last)
        </ul>
    @endif
@endforeach
    