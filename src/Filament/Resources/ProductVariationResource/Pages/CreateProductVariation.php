<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages\ProductVariations;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Models\Product\ProductVariation;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariation extends CreateRecord
{
    protected static string $resource = ProductVariationResource::class;

    protected function afterCreate(): void
    {
        $selectedVariations = $this->data['variations'];
        $productVariationsData = [];
        foreach ($selectedVariations as $variationId => $valueId) {
            $productVariationsData[] = [
                'product_id' => $this->record->id,
                'variation_id' => $variationId,
                'value_id' => $valueId,
            ];
        }
        ProductVariation::insert($productVariationsData);
    }
}
