<div class="alert alert-info" role="alert">
    <p>This document is continually updated.</p>

    <p>The most recent version can always be found online at:</p>

    @if ($options['withWarning'] ?? false)
        <p class="lead">{{ route('scribo.binder', ['binder' => $binder->name]) }}</p>

        <p class="mb-0">
            This binder was downloaded on:
            <br>
            {{ now()->format('g:i A, l, j F Y') }}
        </p>
    @else
        <p class="lead mb-0">{{ route('scribo.binder', ['binder' => $binder->name]) }}</p>
    @endif
</div>


