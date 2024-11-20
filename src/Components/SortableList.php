<?php

namespace YourVendor\FilamentSortable\Components;

use Filament\Forms\Components\Component;

class SortableList extends Component
{
    protected string $view = 'filament-sortable::sortable-list';

    public string $orderColumn = 'sort_order';

    public function orderColumn(string $column): static
    {
        $this->orderColumn = $column;
        return $this;
    }
}