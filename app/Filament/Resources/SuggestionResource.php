<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages;
use App\Filament\Resources\SuggestionResource\RelationManagers;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Suggestion;
use App\Notifications\SuggestionApprovedNotification;
use App\Services\BusinessValidationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
                Forms\Components\TextInput::make('submitter_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('submitter_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('restaurant_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('restaurant_address')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('restaurant_city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('restaurant_state')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('restaurant_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('restaurant_website')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Trust Score Badge - Primera columna para máxima visibilidad
                Tables\Columns\TextColumn::make('trust_score')
                    ->label('Trust')
                    ->badge()
                    ->color(fn (Suggestion $record): string => BusinessValidationService::getScoreColor($record->trust_score))
                    ->formatStateUsing(fn (int $state): string => $state . '%')
                    ->sortable()
                    ->tooltip(fn (Suggestion $record): string =>
                        "Recommendation: " . ucfirst(str_replace('_', ' ', $record->validation_status ?? 'pending'))
                    ),

                Tables\Columns\IconColumn::make('google_verified')
                    ->label('Google')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Suggestion $record): ?string =>
                        $record->google_verified
                            ? "Rating: {$record->google_rating} ⭐ ({$record->google_reviews_count} reviews)"
                            : 'Not verified on Google'
                    ),

                Tables\Columns\IconColumn::make('yelp_verified')
                    ->label('Yelp')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Suggestion $record): ?string =>
                        $record->yelp_verified
                            ? "Rating: {$record->yelp_rating} ⭐ ({$record->yelp_reviews_count} reviews)"
                            : 'Not verified on Yelp'
                    )
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_spam')
                    ->label('Spam')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn (Suggestion $record): string =>
                        $record->is_spam
                            ? "SPAM DETECTED - Score: {$record->spam_score} - Risk: {$record->spam_risk_level}"
                            : "Spam score: {$record->spam_score} - Risk: {$record->spam_risk_level}"
                    )
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_potential_duplicate')
                    ->label('Dup')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn (Suggestion $record): string =>
                        $record->is_potential_duplicate
                            ? 'Potential duplicate - ' . ($record->duplicate_check_data['count'] ?? 0) . ' matches found'
                            : 'No duplicates detected'
                    ),

                Tables\Columns\TextColumn::make('restaurant_name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Suggestion $record): string =>
                        $record->restaurant_city . ', ' . $record->restaurant_state
                    ),

                Tables\Columns\TextColumn::make('submitter_name')
                    ->searchable()
                    ->toggleable()
                    ->description(fn (Suggestion $record): string => $record->submitter_email),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('validation_status')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'auto_approve' => 'success',
                        'auto_approved' => 'success',
                        'quick_review' => 'warning',
                        'full_review' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Suggestion $record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (Suggestion $record) {
                        $record->update(['status' => 'approved']);

                        // Resolve state_id from suggestion's state string (code or name)
                        $state = State::where('code', strtoupper($record->restaurant_state))
                            ->orWhere('name', $record->restaurant_state)
                            ->first();

                        // Create the restaurant record
                        $restaurant = Restaurant::create([
                            'name'               => $record->restaurant_name,
                            'address'            => $record->restaurant_address,
                            'city'               => $record->restaurant_city,
                            'state_id'           => $state?->id,
                            'zip_code'           => $record->restaurant_zip_code,
                            'phone'              => $record->restaurant_phone,
                            'website'            => $record->restaurant_website,
                            'category_id'        => $record->category_id,
                            'description'        => $record->description,
                            'status'             => 'approved',
                            'is_active'          => true,
                            'google_place_id'    => $record->google_place_id,
                            'google_rating'      => $record->google_rating,
                            'google_reviews_count' => $record->google_reviews_count,
                            'yelp_id'            => $record->yelp_id,
                            'yelp_rating'        => $record->yelp_rating,
                            'yelp_review_count'  => $record->yelp_reviews_count,
                            'subscription_status' => null,
                        ]);

                        // Send notification to user or submitter
                        if ($record->user) {
                            $record->user->notify(new SuggestionApprovedNotification($record));
                        }

                        Notification::make()
                            ->success()
                            ->title('Suggestion Approved')
                            ->body("Restaurante \"{$restaurant->name}\" creado en FAMER.")
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSuggestions::route('/'),
            'create' => Pages\CreateSuggestion::route('/create'),
            'edit' => Pages\EditSuggestion::route('/{record}/edit'),
        ];
    }
}
