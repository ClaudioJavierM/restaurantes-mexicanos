<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Restaurant Management';

    protected static ?int $navigationSort = 3;

    public static function getLabel(): string
    {
        return 'Menu Item';
    }

    public static function getPluralLabel(): string
    {
        return 'Menu Items';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurant')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('name')
                            ->label('Name (Spanish)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Tacos al Pastor')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label('Name (English)')
                            ->maxLength(255)
                            ->placeholder('e.g., Pastor Tacos')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Description (Spanish)')
                            ->rows(3)
                            ->placeholder('Describe el platillo en español...')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description_en')
                            ->label('Description (English)')
                            ->rows(3)
                            ->placeholder('Describe the dish in English...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing & Details')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->placeholder('12.99')
                            ->columnSpan(1),

                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options(MenuItem::getCategories())
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('spice_level')
                            ->label('Spice Level')
                            ->options([
                                0 => '0 - No spicy',
                                1 => '1 - Mild 🌶️',
                                2 => '2 - Medium 🌶️🌶️',
                                3 => '3 - Hot 🌶️🌶️🌶️',
                                4 => '4 - Very Hot 🌶️🌶️🌶️🌶️',
                                5 => '5 - Extremely Hot 🌶️🌶️🌶️🌶️🌶️',
                            ])
                            ->placeholder('Select spice level')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dietary & Ingredients')
                    ->schema([
                        Forms\Components\CheckboxList::make('dietary_options')
                            ->label('Dietary Options')
                            ->options(MenuItem::getDietaryOptions())
                            ->columns(3)
                            ->columnSpanFull(),

                        Forms\Components\TagsInput::make('ingredients')
                            ->label('Ingredients')
                            ->placeholder('Add ingredients (press Enter after each)')
                            ->helperText('List main ingredients separated by Enter')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Dish Photo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('menu-items')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->helperText('Upload a high-quality photo of the dish (max 5MB)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Mark as Popular/Signature Dish')
                            ->helperText('This will be featured prominently')
                            ->inline(false)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Currently Available')
                            ->helperText('Uncheck if temporarily out of stock')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png')),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('name')
                    ->label('Dish Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('spice_level')
                    ->label('Spice')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('🌶️', $state) : '-')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant_id')
                    ->label('Restaurant')
                    ->relationship('restaurant', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category')
                    ->options(MenuItem::getCategories())
                    ->multiple(),

                Tables\Filters\Filter::make('popular')
                    ->label('Popular Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_popular', true)),

                Tables\Filters\Filter::make('available')
                    ->label('Available Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_available', true))
                    ->default(),

                Tables\Filters\SelectFilter::make('spice_level')
                    ->options([
                        0 => 'No spicy',
                        1 => '1 Chile 🌶️',
                        2 => '2 Chiles 🌶️🌶️',
                        3 => '3 Chiles 🌶️🌶️🌶️',
                        4 => '4 Chiles 🌶️🌶️🌶️🌶️',
                        5 => '5 Chiles 🌶️🌶️🌶️🌶️🌶️',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_availability')
                    ->label('Toggle')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (MenuItem $record) {
                        $record->update(['is_available' => !$record->is_available]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Mark as Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true])),

                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Mark as Unavailable')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_available' => false])),

                    Tables\Actions\BulkAction::make('mark_popular')
                        ->label('Mark as Popular')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_popular' => true])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
