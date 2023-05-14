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

    protected function getActions(): array
    {
        return [
            // Actions\DeleteAction::make(), // FIXME: Throw an error when using with nested resources
        ];
    }

    protected function mutateFormDataBeforeFill($data): array
    {
        $data['variations'] = [];
        foreach ($this->record->productVariationsValues as $row) {
            $data['variations'][$row['variation_id']] = $row['variation_value_id'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave($data): array
    {
        unset($data['variations']);

        return $data;
    }

    protected function afterSave(): void
    {
        $selectedVariations = $this->data['variations'];
        $productVariationsData = [];
        foreach ($selectedVariations as $variationId => $valueId) {
            $productVariationsData[] = [
                'product_id' => $this->record->id,
                'variation_id' => $variationId,
                'variation_value_id' => $valueId,
            ];
        }
        ProductVariationValue::upsert($productVariationsData, ['product_id', 'product_variation_id']);
    }
}
