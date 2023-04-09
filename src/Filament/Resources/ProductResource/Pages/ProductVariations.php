<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages;

use Almooradi\FilamentEcommerce\Constants\ProductStatus;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Filament\Resources\Pages\Page;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;

class ProductVariations extends Page
{

    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament-ecommerce::products.product-variations';
    
    public $record;

    public $data;

    public function mount()
    {
        $product = Product::findOrFail($this->record);

        abort_if(count($product->variations) == 0, 404, 'Product doesn\'t have any variations');

        // Set sub-heading
        $this->subheading = 'Product: ' . $product->title;

    }

    public function submit()
    {
    }

    protected function getBreadcrumbs(): array
    {
        return [
            route('filament.resources.' . ProductResource::getSlug() . '.index') => 'Products',
            url()->current() => 'Variations',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Heading')
                ->columnSpan('full')
                ->tabs([
                    Tab::make('General')
                        ->schema([
                            TextInput::make('sku')->maxLength(191),
                            Select::make('status')
                                ->disablePlaceholderSelection()
                                ->required()
                                ->default(ProductStatus::DRAFT)
                                ->options(ProductStatus::ALL)
                        ]),
                    Tab::make('Media')
                        ->schema([
                            FileUpload::make('media_files')
                                ->directory('filament-ecommerce/products')
                                ->acceptedFileTypes(['image/*', 'video/mp4', 'video/x-m4v', 'video/*'])
                                ->enableOpen()
                                ->enableDownload()
                                ->multiple()
                                ->enableReordering()
                                ->helperText('You can re-order uploaded files')
                        ]),
                    Tab::make('Price')
                        ->schema([
                            TextInput::make('quantity')
                                ->numeric()
                                ->helperText('Leave empty if you don\'t want to use this feature')
                                ->maxLength(191),
                            TextInput::make('price')
                                ->mask(
                                    fn (TextInput\Mask $mask) => $mask
                                        ->numeric()
                                        ->minValue(0)
                                        ->decimalPlaces(2)
                                        ->decimalSeparator('.')
                                        ->thousandsSeparator(',')
                                )
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    return self::updateDiscountPrice($set, $get);
                                })
                                ->suffix('$')
                                ->maxLength(191),
                            Select::make('discount_type')
                                ->reactive()
                                ->default('none')
                                ->disablePlaceholderSelection()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    return self::updateDiscountPrice($set, $get);
                                })
                                ->suffix(fn ($get) => $get('discount_type') == 'fixed' ? '$' : ($get('discount_type') == 'percentage' ? '%' : null))
                                ->options([
                                    'none' => 'None',
                                    'fixed' => 'Fixed',
                                    'percentage' => 'Percentage',
                                ]),
                            TextInput::make('discount_amount')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(fn ($get) => $get('discount_type') == 'percentage' ? 100 : null)
                                ->hidden(fn ($get) => !in_array($get('discount_type'), ['fixed', 'percentage']))
                                ->required(fn ($get) => in_array($get('discount_type'), ['fixed', 'percentage']))
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    return self::updateDiscountPrice($set, $get);
                                })
                                ->suffix(fn ($get) => $get('discount_type') == 'fixed' ? '$' : ($get('discount_type') == 'percentage' ? '%' : null))
                                ->maxLength(191),
                            TextInput::make('discount_price')
                                ->numeric()
                                ->minValue(0)
                                ->disabled()
                                ->suffix('$')
                                ->dehydrated(false)
                                ->hidden(fn ($get) => !in_array($get('discount_type'), ['fixed', 'percentage'])),
                        ]),
                ])
        ];
    }
}
