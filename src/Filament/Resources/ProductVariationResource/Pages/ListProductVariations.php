<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductVariations extends ListRecords
{
    protected static string $resource = ProductVariationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
