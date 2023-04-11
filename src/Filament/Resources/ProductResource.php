<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources;

use Almooradi\FilamentEcommerce\Constants\Gender;
use Almooradi\FilamentEcommerce\Constants\ProductStatus;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages\CreateProduct;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages\EditProduct;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages\ListProducts;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource\Pages\ProductVariations;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $slug = 'shop/products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Heading')
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->reactive()
                                    ->maxLength(191)
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->autofocus(),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignorable: fn (?Model $record) => $record)
                                    ->maxLength(191),
                                TextInput::make('sku')->maxLength(191),
                                Select::make('gender')
                                    ->default(Gender::UNISEX)
                                    ->disablePlaceholderSelection()
                                    ->options(Gender::ITEM_OPTIONS),
                                Select::make('categories')
                                    ->required()
                                    ->multiple()
                                    ->preload()
                                    ->relationship('categories', 'title'),
                                Select::make('show_in')
                                    ->options([
                                        'home' => 'Home',
                                    ])
                                    ->multiple(),
                                ColorPicker::make('highlight_label_background_color'),
                                ColorPicker::make('highlight_label_text_color'),
                                TextInput::make('highlight_label_text')->maxLength(191),
                                Select::make('status')
                                    ->disablePlaceholderSelection()
                                    ->required()
                                    ->default(ProductStatus::DRAFT)
                                    ->options(ProductStatus::ALL)
                            ]),
                        Tab::make('Content')
                            ->schema([
                                Textarea::make('short_description')
                                    ->maxLength(300),
                                MarkdownEditor::make('long_description')
                                    ->fileAttachmentsDirectory('products')
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
                        Tab::make('Variation')
                            ->schema([
                                Select::make('variations')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->relationship('variations', 'name')
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        // dd(Product::first()->variations());
        return $table
            ->columns([
                TextColumn::make('title')->words(7)->tooltip(fn (TextColumn $column): ?string => $column->getState())->searchable(),
                TextColumn::make('categories')->formatStateUsing(fn (Collection | null $state): string => $state ? implode(', ', $state->pluck('title')->toArray()) : '')->searchable(),
                TextColumn::make('gender')->formatStateUsing(fn (string | null $state): string => Gender::ITEM_OPTIONS[$state] ?? ''),
                BadgeColumn::make('status')->enum(ProductStatus::ALL)->colors(ProductStatus::FILAMENT_BADGE_COLORS),
                TextColumn::make('show_in')->formatStateUsing(fn (string | null $state): string => ucfirst($state)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('variations')
                    ->visible(fn (Product $record): bool => count($record->variations) > 0)
                    ->url(fn (Product $record): string => route('filament.resources.' . ProductVariationResource::getSlug() . '.index', $record))
                    ->icon('heroicon-o-color-swatch')
                    ->color('success'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    private static function updateDiscountPrice(Closure $set, Closure $get)
    {
        $price = $get('price');
        $discount_price = $get('price');
        $discount_amount = $get('discount_amount');

        if ($get('discount_type') == 'fixed') {
            $discount_price = $price - $discount_amount;
        } else if ($get('discount_type') == 'percentage') {
            $discount_price = $price - $price * $discount_amount / 100;
        }

        $set('discount_price', $discount_price);
    }
}
