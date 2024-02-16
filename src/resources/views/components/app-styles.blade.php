<x-scribo::app-bootstrap-styles />

<x-scribo::app-font-styles />

<style>
    .w-256-px, #sidebar-offcanvas, #sidebar-collapsible, #sidebar-collapsible > div {
        width: 256px;
    }
    #onthispage, #middle {
        overflow-y: auto;
    }
    @media (max-width: 992px) {
        #onthispage {
            width: 100%;
        }
        #middle, #onthispage {
            overflow-y: visible;
        }
        #content {
            overflow-y: auto;
        }
    }

    .color-emoji {
        font-family: "Noto Color Emoji";
    }

    :root {
        --bs-font-serif: "Source Serif 4";
        --bs-font-sans-serif: "Source Sans 3", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        --bs-font-monospace: "Source Code Pro", SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        --bs-body-font-family: var(--bs-font-serif);
    }
</style>
