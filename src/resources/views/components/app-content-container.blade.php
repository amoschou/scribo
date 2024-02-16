<div id="content" class="flex-grow-1 d-flex flex-column flex-lg-row">
    @if (! $isBinder && request()->input('format') === 'pdf')
        <div class="flex-shrink-0">
            <div class="small lh-sm card border-0 rounded-0">
                <div class="card-header border-0 rounded-0">
                    {{ $nodeItem->findPart()->findPartwiseOrder() }}&ensp;{!! $nodeItem->findPart()->metadata('formattedTitle') !!}
                </div>
            </div>

            <h1 class="display-3">
                <x-scribo::app-partwise-tag :tag="$nodeItem->findPartwiseOrder()" />{!! $nodeItem->metadata('formattedTitle') !!}
            </h1>

            {{-- This is displayed at the top of an individual pdf item --}}
        </div>
    @endif

    <div id="onthispage" class="flex-shrink-0 order-lg-last w-256-px @if ($isBinder) d-none d-lg-block @endif ">
        @if ($nodeItem->type === 'file')
            <x-scribo::app-on-this-page :nodeItem="$nodeItem" />
        @else
            <div class="small lh-sm card border-0 rounded-0">
                <div class="card-header rounded-0">About this page</div>

                <div class="list-group list-group-flush rounded-0">
                    @if (request()->input('format') !== 'pdf')
                        <a class="list-group-item list-group-item-action" href="/{{ request()->path() }}.pdf">Download as PDF</a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div id="middle" class="flex-grow-1 order-lg-first d-flex flex-column d-sm-flex flex-sm-column">
        @if (request()->input('format') === 'html' || ! $isBinder)
            <div class="small lh-sm card border-0 rounded-0">
                <div class="card-header rounded-0">
                    <x-scribo::app-breadcrumbs :nodeItem="$nodeItem" :isBinder="$isBinder" />
                </div>
            </div>
        @endif

        <main class="flex-grow-1 flex-sm-grow-1 container-fluid pt-2">
            @if ($nodeItem->type === 'file')
                {!! $nodeItem->getHtml() !!}
            @endif

            @if ($nodeItem->type === 'folder')
                @if ($isBinder)
                    <x-scribo::app-binder-content :binder="$binder" :nodeItem="$nodeItem" :isContent="$isContent" :options="$options" />
                @else
                    <x-scribo::app-folder-content :nodeItem="$nodeItem" :options="$options" />
                @endif
            @endif
        </main>

        <footer class="container-fluid py-2">
            <x-scribo::app-footer />
        </footer>
    </div>
</div>
