<div x-data="sortableList({
        orderColumn: '{{ $orderColumn }}',
    })" x-sortable class="filament-sortable-list">
    {{ $slot }}
</div>