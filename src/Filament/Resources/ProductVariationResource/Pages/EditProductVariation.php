<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Almooradi\FilamentEcommerce\Models\Product\ProductVariationValue;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use SevendaysDigital\FilamentNestedResources\ResourcePages\NestedPage;

class EditProductVariation extends EditRecord
{
    use NestedPage;

    protected static string $resource = ProductVariationResource::class;

    protected function getSubheading(): string | Htmlable | null
    {
        $parentProduct = Product::findOrFail($this->record->parent_product_id);

        return 'Parent Product: ' . $parentProduct->title;
    }

    // protected function getActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }

    protected function afterSave(): void
    {
        $selectedVariations = $this->data['variations'];
        $productVariationsData = [];
        foreach ($selectedVariations as $variationId => $valueId) {
            $productVariationsData[] = [
                'product_variation_id' => $variationId,
                'variation_value_id' => $valueId,
            ];
        }
        ProductVariationValue::insert($productVariationsData);
    }
}
