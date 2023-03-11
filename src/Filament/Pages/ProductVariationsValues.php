<?php

namespace App\Filament\Pages;

use App\Models\Setting as ModelsSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ProductVariationsValues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.products.variations.values';

    protected static ?string $title = 'Product Variations Values';

    protected array $singleImagesFields = ['about_us.image'];

    public function mount()
    {
        $settings = ModelsSetting::all()->mapWithKeys(function ($setting) {
            $key = $setting->section . '.' . $setting->key;
            $value = $setting->value;

            // We check if the image/file exist (file_exists), because if it's not, the "remove button" doesn't appear and it just keeps loading
            if (in_array($key, $this->singleImagesFields) && !file_exists(storage_path('app/public/' . $value))) {
                $value = null;
            }

            return [
                "settings.$key" => $value
            ];
        })->toArray();

        $this->form->fill($settings);
    }

    public function submit()
    {
        $this->form->getState();

        $state = [];
        foreach ($this->settings as $sectionKey => $sectionData) {
            foreach ($sectionData as $key => $value) {
                if (in_array($sectionKey . '.' . $key, $this->singleImagesFields)) {
                    $value = current($value ?? []);
                }

                $state[] = [
                    'key'     => $key,
                    'section' => $sectionKey,
                    'value'   => is_array($value) ? json_encode($value) : $value
                ];
            }
        }

        ModelsSetting::upsert($state, ['key', 'section'], ['value']);

        $this->notify('success', 'Settings has been updated.');
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Settings',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('General')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.general.app_name')
                                ->label('Site Name')
                                ->maxLength(191),
                        ]),
                    Grid::make()
                        ->schema([
                            FileUpload::make('settings.general.app_logo_light')
                                ->directory('settings')
                                ->label('Logo - Light')
                                ->image()
                                ->enableOpen()
                                ->enableDownload(),
                            FileUpload::make('settings.general.app_logo_dark')
                                ->directory('settings')
                                ->label('Logo - Dark')
                                ->image()
                                ->enableOpen()
                                ->enableDownload()
                        ]),
                ]),
            Section::make('About Us')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.about_us.home_section_subtitle')
                                ->label('Home Section Subtitle')
                                ->maxLength(191),
                            TextInput::make('settings.about_us.title')
                                ->label('Title')
                                ->maxLength(191)
                        ]),
                    MarkdownEditor::make('settings.about_us.short_description')
                        ->label('Short Description')
                        ->maxLength(500),
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.about_us.button_label')
                                ->label('Button Label')
                                ->maxLength(191),
                            TextInput::make('settings.about_us.button_link')
                                ->label('Button Link')
                                ->maxLength(191),
                        ]),
                    FileUpload::make('settings.about_us.image')
                        ->directory('settings')
                        ->label('Image')
                        ->image()
                        ->enableOpen()
                        ->enableDownload()
                ]),
            Section::make('Contact Us')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.contact_us.home_section_subtitle')
                                ->label('Home Section Subtitle')
                                ->maxLength(191),
                            TextInput::make('settings.contact_us.title')
                                ->label('Title')
                                ->maxLength(191)
                        ]),
                    TextInput::make('settings.contact_us.email')
                        ->label('Email')
                        ->maxLength(191),
                    TextInput::make('settings.contact_us.location')
                        ->label('Location')
                        ->maxLength(191),
                    TextInput::make('settings.contact_us.phone')
                        ->label('Phone')
                        ->maxLength(191),
                    TextInput::make('settings.contact_us.footer')
                        ->label('Footer')
                        ->maxLength(191)
                ]),
            Section::make('Features')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.features.home_section_subtitle')
                                ->label('Home Section Subtitle')
                                ->maxLength(191),
                            TextInput::make('settings.features.title')
                                ->label('Title')
                                ->maxLength(191)
                        ]),
                ]),
            Section::make('Testimonials')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('settings.testimonials.home_section_subtitle')
                                ->label('Home Section Subtitle')
                                ->maxLength(191),
                            TextInput::make('settings.testimonials.title')
                                ->label('Title')
                                ->maxLength(191)
                        ]),
                ]),
        ];
    }
}
