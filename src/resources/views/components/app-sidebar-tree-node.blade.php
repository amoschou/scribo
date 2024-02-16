<p>{{ $nodeLabel }}</p>

@foreach ($tree as $key => $item)
    <x-scribo::app-sidebar-tree-node :nodeLabel="$key" :tree="$item" />
@endforeach
