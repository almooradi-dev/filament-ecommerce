<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources;

use Almooradi\FilamentEcommerce\Constants\ProductStatus;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages\CreateProductVariation;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages\EditProductVariation;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages\ListProductVariations;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Almooradi\FilamentEcommerce\Models\Variation\Variation;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use SevendaysDigital\FilamentNestedResources\NestedResource;

class ProductVariationResource extends NestedResource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'variations';

    public static function getParent(): string
    {
        return ProductResource::class;
    }

    public static function getParentAccessor(): string
    {
        return 'parentProduct';
    }

    public static function getPluralModelLabel(): string
    {
        return 'variations';
    }

    public static function getEloquentQuery(string|int|null $parent = null): Builder
    {
        $query = static::getModel()::query();
        $parentModel = static::getParent()::getModel();
        $key = (new $parentModel)->getKeyName();
        $query->whereHas(
            static::getParentAccessor(),
            fn (Builder $builder) => $builder->where($key, '=', $parent ?? static::getParentId())
        );

        return $query;
    }

    public static function form(Form $form): Form
    {
        $product = Product::find(request()->product);

        $variationsSelects = [];
        if ($product) {
            $product->loadMissing('variations.values');
            foreach ($product->variations as $variation) {
                $variationsSelects[] = Select::make('variations.' . $variation->id)
                    ->label($variation->name)
                    ->required()
                    ->options($variation->values->pluck('value', 'id'));
            }
        }

        return $form
            ->schema([
                Tabs::make('Heading')
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make('Variations')
                            ->schema($variationsSelects),
                        Tab::make('General')
                            ->schema([
                                Select::make('parent_product_id')
                                    ->relationship('parentProduct', 'title')
                                    ->default($product?->id)
                                    ->disabled(true),
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
                                        return ProductResource::updateDiscountPrice($set, $get);
                                    })
                                    ->suffix('$')
                                    ->maxLength(191),
                                Select::make('discount_type')
                                    ->reactive()
                                    ->default('none')
                                    ->disablePlaceholderSelection()
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, Closure $get) {
                                        return ProductResource::updateDiscountPrice($set, $get);
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
                                        return ProductResource::updateDiscountPrice($set, $get);
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductVariations::route('/'),
            'create' => CreateProductVariation::route('/create'),
            'edit' => EditProductVariation::route('/{record}/edit'),
        ];
    }

    // public static function getUrl($name = 'index', $params = [], $isAbsolute = true): string
    // {
    //     // Add "product" parameter
    //     if (!isset($params['product'])) {
    //         $params['product'] = request()->product;
    //     }

    //     $routeBaseName = static::getRouteBaseName();

    //     return route("{$routeBaseName}.{$name}", $params, $isAbsolute);
    // }
}
