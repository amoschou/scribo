<x-scribo::app-standard-layout
    :nodeItem="$data['nodeItem']"
    :isBinder="$isBinder ?? false"
    :isContent="$isContent ?? false"
    :isPdf="false"
    :options="$options ?? []"
    :binder="$data['binder']"
    :alert="The PDF that you requested is currently unavailable. Try again in a few minutes."
/>