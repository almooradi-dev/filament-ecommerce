<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources;

use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource\Pages\CreateVariation;
use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource\Pages\EditVariation;
use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource\Pages\ListVariations;
use Almooradi\FilamentEcommerce\Models\Variation\Variation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class VariationResource extends Resource
{
    protected static ?string $model = Variation::class;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(191)
                    ->autofocus(),
                TextInput::make('display_name')
                    ->required()
                    ->maxLength(191),
                Repeater::make('values')
                    ->relationship()
                    ->schema([
                        TextInput::make('value')->required(),
                    ]),
                Toggle::make('status')->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('display_name'),
                TextColumn::make('status'),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVariations::route('/'),
            'create' => CreateVariation::route('/create'),
            'edit' => EditVariation::route('/{record}/edit'),
        ];
    }
}
