<div class="d-none d-md-block overflow-y-auto flex-shrink-0">
    <div id="sidebar-collapsible" class="collapse collapse-horizontal show">
        <x-scribo::app-sidebar :id="'a'" :collapsible=true :offcanvas=false :nodeItem="$nodeItem"/>
    </div>
</div>

<div id="sidebar-offcanvas" class="offcanvas-md offcanvas-start d-md-none overflow-y-auto" tabindex="-1">
    <x-scribo::app-sidebar :id="'b'" :collapsible=false :offcanvas=true :nodeItem="$nodeItem" />
</div>
