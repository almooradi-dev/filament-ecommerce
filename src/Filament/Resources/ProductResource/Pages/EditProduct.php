<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
