<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
