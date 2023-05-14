<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Almooradi\FilamentEcommerce\Models\Product\ProductVariationValue;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use SevendaysDigital\FilamentNestedResources\ResourcePages\NestedPage;

class CreateProductVariation extends CreateRecord
{
    use NestedPage;

    protected static string $resource = ProductVariationResource::class;

    protected static bool $canCreateAnother = false; // FIXME: When clicking on the "Create and create another" button, the "product" route parameter is missing, so an error occurs (Maybe to fix it, we can refresh the page)

    protected function getSubheading(): string | Htmlable | null
    {
        $parentProduct = Product::findOrFail($this->getParentId());

        return 'Parent Product: ' . $parentProduct?->title;
    }

    protected function afterCreate(): void
    {
        $selectedVariations = $this->data['variations'] ?? [];
        if (count($selectedVariations) > 0) {
            $productVariationsData = [];
            foreach ($selectedVariations as $variationId => $valueId) {
                $productVariationsData[] = [
                    'product_id' => $this->record->id,
                    'variation_id' => $variationId,
                    'variation_value_id' => $valueId,
                ];
            }
            ProductVariationValue::insert($productVariationsData);
        }
    }
}
