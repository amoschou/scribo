<div class="small lh-sm card border-0 border-bottom rounded-0">
    <div class="card-header rounded-0">This binder</div>

    <div class="list-group list-group-flush rounded-0">
        <div class="list-group-item">
            <p class="mb-2">Outline:</p>

            @if (request()->boolean('sidebar', true))
                <x-scribo::app-sidebar-item :tree="$nodeItem->getBinder()->getTree('THIS SHOULD NEVER HAPPEN')" :root="true"/>
            @endif
        </div>

        <a class="list-group-item list-group-item-action border-bottom-0" href="{{ route('scribo.complete.binder.pdf', ['binder' => $nodeItem->getBinder()->name]) }}">Download PDF</a>

        {{--
        <button type="button" class="list-group-item list-group-item-action border-bottom-0" data-bs-toggle="collapse" data-bs-target="#download-collapse-{{ $id }}" aria-expanded="false" aria-controls="download-collapse-{{ $id }}">Download PDF</button>

        <div id="download-collapse-{{ $id }}" class="collapse">
            <div class="list-group-item text-bg-warning d-grid gap-2 border-0">
                <div>Downloading the entire binder could take a few minutes, please wait and do not cancel the process.</div>

                <a class="btn btn-sm btn-outline-dark" href="{{ route('scribo.complete.binder.pdf', ['binder' => $nodeItem->getBinder()->name]) }}">Download</a>
            </div>
        </div>
        --}}
    </div>
</div>
