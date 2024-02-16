@php
    $withPageNumbers = isset($options['pageNumbers']);
@endphp

@if (request()->input('format') === 'pdf')
    <style>
        .accordion-button::after {
            display: none;
        }
    </style>
@endif

@if ($isContent)
    <h1>Table of contents</h1>
@else
    <h1 class="display-1 py-4 text-center">{{ $nodeItem->metadata('title') }}</h1>

    <x-scribo::app-binder-warning :binder="$binder" :options="$options" />
@endif

@if ($isContent)
    <div class="accordion accordion-flush @if (request()->input('format') === 'pdf') open @endif">
        @foreach ($nodeItem->getTree('') as $key => $item)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    @if (request()->input('format') === 'html')
                        <button class="accordion-button collapsed bg-secondary-subtle pe-3" type="button" data-bs-toggle="collapse" data-bs-target="#contents-{{ $key }}" aria-expanded="false" aria-controls="contents-{{ $key }}">
                            <div class="d-flex w-100 justify-content-between">
                                <span>
                                    <x-scribo::app-partwise-tag :tag="$item->object->findPartwiseOrder()" />{{ $item->object->metadata('title') }}
                                </span>

                                @if ($withPageNumbers)
                                    <small>{{ $options['pageNumbers'][$item->object->getLocalPath()] ?? '[?]' }}</small>
                                @endif
                            </div>
                        </button>
                    @endif

                    @if (request()->input('format') === 'pdf')
                        <div class="accordion-button collapsed bg-secondary-subtle pe-3">
                            <div class="d-flex w-100 justify-content-between">
                                <span>
                                    <x-scribo::app-partwise-tag :tag="$item->object->findPartwiseOrder()" />{{ $item->object->metadata('title') }}
                                </span>

                                @if ($withPageNumbers)
                                    <small>{{ $options['pageNumbers'][$item->object->getLocalPath()] ?? '[?]' }}</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </h2>

                <div id="contents-{{ $key }}" class="accordion-collapse collapse @if (request()->input('format') === 'pdf') show @endif">
                    <div class="accordion-body p-0">
                        <x-scribo::app-contents-card :item="$item->object" :withPageNumbers="$withPageNumbers" :options="$options" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
