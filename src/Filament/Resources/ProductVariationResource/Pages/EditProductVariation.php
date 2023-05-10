<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditProductVariation extends EditRecord
{
    protected static string $resource = ProductVariationResource::class;

    protected function getSubheading(): string | Htmlable | null
    {
        dd($this->record);
        
        $parentProduct = Product::findOrFail(static::$resource::getParentId());

        return 'Parent Product: ' . $parentProduct->title;
    }
    
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
