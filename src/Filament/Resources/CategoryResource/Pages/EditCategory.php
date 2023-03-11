<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
