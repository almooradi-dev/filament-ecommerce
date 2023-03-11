<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources;

use Almooradi\FilamentEcommerce\Constants\CategoryStatus;
use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource\Pages\EditCategory;
use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource\Pages\ListCategories;
use Almooradi\FilamentEcommerce\Models\Category;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
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
                Textarea::make('short_description'),
                Select::make('show_in')
                    ->options([
                        'home' => 'Home',
                    ])
                    ->multiple(),
                Select::make('parent_category_id')
                    ->label('Parent Category')
                    // ->options(Category::where('id', '!=', '1')->get()->pluck('title', 'id')) // TODO: Important, to avoid infinite loop in case we selct the same cateogry (itself)
                    ->options(Category::whereIsParent()->get()->pluck('title', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->disablePlaceholderSelection()
                    ->required()
                    ->default(CategoryStatus::ACTIVE)
                    ->options(CategoryStatus::ALL),
                FileUpload::make('image')
                    ->directory('shop/categories')
                    ->image()
                    ->enableOpen()
                    ->enableDownload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('show_in')->formatStateUsing(fn (string | null $state): string => ucfirst($state)),
                TextColumn::make('status')->formatStateUsing(fn (string | null $state): string => CategoryStatus::ALL[$state] ?? ''),
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
