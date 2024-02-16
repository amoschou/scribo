@php
    use Illuminate\Support\Carbon;
@endphp

<div class="small lh-sm card border-0 border-bottom rounded-0">
    <div class="card-header rounded-0">About this page</div>

    <div class="list-group list-group-flush rounded-0">
        @if (request()->input('format') !== 'pdf')
            <a class="list-group-item list-group-item-action" href="/{{ request()->path() }}.pdf">Download as PDF</a>
        @endif

        <div class="list-group-item">
            <p class="mb-2">Outline:</p>

            @php $level = 0 @endphp

            @foreach ($nodeItem->getToc()['items'] as $item)
                @while ($level !== $item['level'])
                    @if ($level < $item['level'])
                        <ul class="{{ match ($level) {
                            0 => 'xx-list-unstyled mb-0',
                            1 => '',
                            default => '',
                        } }}">

                        @php $level++ @endphp
                    @endif

                    @if ($level > $item['level'])
                        @php $level-- @endphp

                        </ul>
                    @endif
                @endwhile

                <li class="{{
                    match ($level) {
                        0 => '',
                        1 => '',
                        2 => '',
                        default => '',
                    }
                }}">
                    @if ($level <= 2) <strong> @endif
                        {!! $item['formattedText'] !!}
                    @if ($level <= 2) </strong> @endif
                </li>
            @endforeach

            @while ($level > 0)
                @php $level-- @endphp

                </ul>
            @endwhile
        </div>

        <li class="list-group-item">
            @if (request()->input('format') === 'pdf')
                Last&nbsp;updated: By <span class="text-monospace">{{ $nodeItem->getGitDetails('author') }}</span> at {!! $nodeItem->getGitDetails('date') !!}
            @endif

            @if (request()->input('format') === 'html')
                Last&nbsp;update: {!! str_replace(' ', '&nbsp;', Carbon::parse($nodeItem->getGitDetails('date', false))->setTimezone(config('app.timezone'))->format('H:i j/m/Y')) !!} (<span class="text-monospace">{{ $nodeItem->getGitDetails('author') }}</span>)
            @endif
        </li>

        @if ($nodeItem->metadata('editLink'))
            @if (request()->input('format') === 'pdf')
                <li class="list-group-item">Source:<br><a href="{{ $nodeItem->metadata('sourceLink') }}">{{ $nodeItem->metadata('sourceLink') }}</a></li>
            @else
                <a class="list-group-item list-group-item-action" href="{{ $nodeItem->metadata('editLink') }}" target="_blank">Edit</a>
            @endif
        @endif
    </div>
</div>
