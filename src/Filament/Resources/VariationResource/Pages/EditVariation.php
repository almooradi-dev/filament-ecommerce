<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\VariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariation extends EditRecord
{
    protected static string $resource = VariationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
