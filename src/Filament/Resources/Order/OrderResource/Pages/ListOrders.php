<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
