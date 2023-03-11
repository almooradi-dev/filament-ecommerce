<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
