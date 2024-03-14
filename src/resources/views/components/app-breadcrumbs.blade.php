@php
    $binder = $nodeItem->getBinder();

    $partwiseOrders = $binder->getPartwiseOrders();

    $withHomeAndContents = request()->input('format') === 'html';

    $breadcrumbs = $nodeItem->breadcrumbs($withHomeAndContents);
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        @if ($withHomeAndContents)
            <li class="breadcrumb-item">
                {{-- <a href="{{ route('scribo.binder', ['binder' => $breadcrumbs[0]['binder']]) }}">Binder</a> --}}
                <a href="{{ route('scribo.binder', ['binder' => $binder->name]) }}">Home</a>
            </li>
        @endif

        @foreach ($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item">
                <x-scribo::app-partwise-tag :tag="$partwiseOrders[$breadcrumb['partial-path']]" /><a href="{{ route('scribo.path', ['binder' => $binder->name, 'path' => $breadcrumb['partial-path']]) }}">{{ $breadcrumb['title'] }}</a>
            </li>
        @endforeach

        @if (! $isBinder)
            <li class="breadcrumb-item active" aria-current="page"><x-scribo::app-partwise-tag :tag="$nodeItem->findPartwiseOrder()" />{!! $nodeItem->metadata('formattedTitle') !!}</li>
        @endif
    </ol>
</nav>
