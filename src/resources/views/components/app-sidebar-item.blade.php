@php
    $intToRoman = new Romans\Filter\IntToRoman;
@endphp

@foreach ($tree as $key => $treeItem)
    @if ($loop->first)
        <ul class="{{ $root ? 'list-unstyled' : '' }} mb-0">
    @endif

    @if ($treeItem->object->type === 'file')
        <li class="list-unstyled">
            <x-scribo::app-partwise-tag :tag="$treeItem->object->findPartwiseOrder()" />
           <a href="{{ $treeItem->object->href() }}">{!! $treeItem->object->metadata('formattedTitle') !!}</a>
        </li>
    @endif

    @if ($treeItem->object->type === 'folder')
        <details>
            <summary>
                <x-scribo::app-partwise-tag :tag="$treeItem->object->findPartwiseOrder()" />{!! $treeItem->object->metadata('formattedTitle') !!}
            </summary>

            <x-scribo::app-sidebar-item :tree="$treeItem->tree" :root="false" />
        </details>
    @endif

    @if ($loop->last)
        </ul>
    @endif
@endforeach
