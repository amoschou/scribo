@foreach ($item->getFlatTree() as $key => $subitem)
    @if ($loop->first)
        <div class="list-group list-group-flush">
    @endif

    <a class="list-group-item list-group-item-action" style="padding-left: {{ 20 + 24 * $subitem->depth }}px;" href="{{ $subitem->object->href() }}">
        <div class="d-flex w-100 justify-content-between">
            <span><x-scribo::app-partwise-tag :tag="$subitem->object->findPartwiseOrder()" />{!! $subitem->object->metadata('formattedTitle') !!}</span>

            @if ($withPageNumbers)
                <small>{{ $options['pageNumbers'][$subitem->object->getLocalPath()] ?? '[?]' }}</small>
            @endif
        </div>
    </a>

    @if ($loop->last)
        </div>
    @endif
@endforeach
