<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\Order;

use Almooradi\FilamentEcommerce\Constants\Order\OrderShippingStatus;
use Almooradi\FilamentEcommerce\Constants\Order\OrderStatus;
use Almooradi\FilamentEcommerce\Constants\Order\PaymentMethod;
use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource\Pages\CreateOrder;
use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource\Pages\EditOrder;
use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource\Pages\ListOrders;
use Almooradi\FilamentEcommerce\Models\Order\Order;
use App\Models\Geo\Country;
use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
	protected static ?string $model = Order::class;

	protected static ?string $navigationGroup = 'Shop';

	protected static ?string $navigationIcon = 'heroicon-o-tag';

	protected static ?string $slug = 'shop/orders';

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Tabs::make('Tabs')
					->tabs([
						Tabs\Tab::make('Order Details')
							->schema([
								TextInput::make('uid')
									->label('Order Number')
									->required()
									->disabled(),
								Select::make('user')
									->relationship('user', 'first_name')
									->required()
									->disabled(),
								Select::make('payment_method')
									->options(PaymentMethod::ALL)
									->required()
									->disabled(),
								Select::make('status') // TODO: Add badge color
									->options(OrderStatus::ALL)
									->required(),
								TextInput::make('subtotal')
									->mask(
										fn (TextInput\Mask $mask) => $mask
											->numeric()
											->minValue(0)
											->decimalPlaces(2)
											->decimalSeparator('.')
											->thousandsSeparator(',')
									)
									->suffix('$')
									->maxLength(191)
									->required()
									->disabled(),
								TextInput::make('total')
									->mask(
										fn (TextInput\Mask $mask) => $mask
											->numeric()
											->minValue(0)
											->decimalPlaces(2)
											->decimalSeparator('.')
											->thousandsSeparator(',')
									)
									->suffix('$')
									->maxLength(191)
									->required()
									->disabled(),
								Textarea::make('notes')
									->disabled(),
							]),
						Tabs\Tab::make('Billing Details') // TODO: Add also shipping details (address)
							->schema([
								Fieldset::make('Metadata')
									->relationship('billingDetails')
									->schema([
										TextInput::make('first_name')
											->required()
											->disabled(),
										// TextInput::make('father_name')
										// 	->required()
										// 	->disabled(),
										TextInput::make('last_name')
											->required() // TODO: add a condition for all required fields, so developer can editthem from outside the package
											->disabled(),
										TextInput::make('email')
											->required()
											->disabled(),
										TextInput::make('country_code')
											->required()
											->disabled(),
										TextInput::make('phone')
											->required()
											->disabled(),
										Select::make('country')
											->relationship('countryModel', 'name')
											->required()
											->disabled()
											->reactive(),
										Select::make('state')
											->relationship('stateModel', 'name', fn ($query, $get) => $query->where('country', $get('country')))
											->searchable()
											->required()
											->disabled()
											->reactive(),
										Select::make('city')
											->relationship('cityModel', 'name', fn ($query, $get) => $query->where('state_code', $get('state')))
											->searchable()
											->required()
											->disabled(),
										TextInput::make('address_1')
											->required()
											->disabled(),
										TextInput::make('address_2')
											->required()
											->disabled(),
										TextInput::make('zip_code')
											->required()
											->disabled(),
									])
							]),
						Tabs\Tab::make('Shipping')
							->schema([
								TextInput::make('shipping_cost') // TODO: Add badge color
									->mask(
										fn (TextInput\Mask $mask) => $mask
											->numeric()
											->minValue(0)
											->decimalPlaces(2)
											->decimalSeparator('.')
											->thousandsSeparator(',')
									)
									->suffix('$')
									->maxLength(191)
									->required()
									->disabled(),
								Select::make('shipping_status')
									->options(OrderShippingStatus::ALL)
									->required(),
							]),
					])
					->columnSpanFull()
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('uid')->label('#'),
				TextColumn::make('user')->formatStateUsing(fn (User | null $state): string|null => $state->full_name ?? ($state->first_name . ' ' . $state->last_name))->searchable(),
				// TextColumn::make('payment_method')->formatStateUsing(fn (int $state): string|null => isset(PaymentMethod::ALL[$state]) ? PaymentMethod::ALL[$state] : ''),
				TextColumn::make('status')->formatStateUsing(fn (int $state): string|null => isset(OrderStatus::ALL[$state]) ? OrderStatus::ALL[$state] : ''),
				TextColumn::make('shipping_status')->formatStateUsing(fn (int $state): string|null => isset(OrderShippingStatus::ALL[$state]) ? OrderShippingStatus::ALL[$state] : ''),
				TextColumn::make('total')->formatStateUsing(fn (int $state): string => $state . '$'),
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
			'index' => ListOrders::route('/'),
			'create' => CreateOrder::route('/create'),
			'edit' => EditOrder::route('/{record}/edit'),
		];
	}
}
