<?php

namespace YourVendor\FilamentSortable;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSortableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sortable')
            ->hasConfigFile()
            ->hasViews();

        // Register assets
        FilamentAsset::register([
            AlpineComponent::make('filament-sortable', __DIR__ . '/../resources/dist/filament-sortable.js'),
            Css::make('filament-sortable-styles', __DIR__ . '/../resources/dist/filament-sortable.css'),
        ]);
    }
}
