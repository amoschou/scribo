<x-scribo::app-standard-layout
    :nodeItem="$data['nodeItem']"
    :isBinder="$data['isBinder'] ?? false"
    :isContent="$data['isContent'] ?? false"
    :isPdf="false"
    :options="$data['options'] ?? []"
    :binder="$data['binder']"
    alert="The PDF that you requested is currently unavailable. Try again in a few minutes."
/>