<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\VariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVariations extends ListRecords
{
    protected static string $resource = VariationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
