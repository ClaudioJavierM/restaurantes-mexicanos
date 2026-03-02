<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamerScoreRequestResource\Pages;
use App\Models\FamerScoreRequest;
use App\Mail\FamerScoreReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class FamerScoreRequestResource extends Resource
{
    protected static ?string $model = FamerScoreRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Score Leads';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(3),

                Forms\Components\Section::make('Restaurant Information')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('restaurant_name')
                            ->maxLength(255)
                            ->helperText('For external restaurants not in our DB'),
                        Forms\Components\TextInput::make('restaurant_city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('restaurant_state')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('External IDs')
                    ->schema([
                        Forms\Components\TextInput::make('yelp_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('google_place_id')
                            ->maxLength(255),
                    ])->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Status & Tracking')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'sent' => 'Sent',
                                'opened' => 'Opened',
                                'clicked' => 'Clicked',
                                'claimed' => 'Claimed',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_owner')
                            ->label('Identified as Owner'),
                        Forms\Components\Toggle::make('marketing_consent')
                            ->label('Marketing Consent'),
                        Forms\Components\DateTimePicker::make('email_sent_at'),
                        Forms\Components\DateTimePicker::make('email_opened_at'),
                        Forms\Components\DateTimePicker::make('email_clicked_at'),
                    ])->columns(3),

                Forms\Components\Section::make('UTM Tracking')
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('utm_medium')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('utm_campaign')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('referrer')
                            ->maxLength(255),
                    ])->columns(4)
                    ->collapsed(),

                Forms\Components\Section::make('Technical Info')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->maxLength(45),
                        Forms\Components\Textarea::make('user_agent')
                            ->maxLength(500),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('restaurant_name')
                    ->label('Restaurant')
                    ->searchable()
                    ->description(fn (FamerScoreRequest $record): string =>
                        $record->restaurant_city
                            ? "{$record->restaurant_city}, {$record->restaurant_state}"
                            : ($record->restaurant?->name ?? '-')
                    ),

                Tables\Columns\IconColumn::make('is_owner')
                    ->label('Owner')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'sent' => 'info',
                        'opened' => 'warning',
                        'clicked' => 'success',
                        'claimed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('marketing_consent')
                    ->label('Marketing')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('famerScore.overall_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 70 => 'info',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('utm_source')
                    ->label('Source')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email_sent_at')
                    ->label('Sent')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'opened' => 'Opened',
                        'clicked' => 'Clicked',
                        'claimed' => 'Claimed',
                    ]),
                Tables\Filters\Filter::make('is_owner')
                    ->label('Owners Only')
                    ->query(fn (Builder $query) => $query->where('is_owner', true)),
                Tables\Filters\Filter::make('marketing_consent')
                    ->label('With Marketing Consent')
                    ->query(fn (Builder $query) => $query->where('marketing_consent', true)),
                Tables\Filters\Filter::make('not_claimed')
                    ->label('Not Claimed')
                    ->query(fn (Builder $query) => $query->notClaimed()),
                Tables\Filters\Filter::make('has_restaurant')
                    ->label('Linked to Restaurant')
                    ->query(fn (Builder $query) => $query->whereNotNull('restaurant_id')),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('Resend Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (FamerScoreRequest $record) => $record->restaurant_id !== null)
                    ->action(function (FamerScoreRequest $record) {
                        $restaurant = $record->restaurant;
                        $score = $record->famerScore ?? $restaurant?->famerScore;

                        if (!$score) {
                            Notification::make()
                                ->danger()
                                ->title('Cannot Send')
                                ->body('No score available for this restaurant.')
                                ->send();
                            return;
                        }

                        $restaurantData = [
                            'id' => $restaurant->id,
                            'name' => $restaurant->name,
                            'city' => $restaurant->city,
                            'state' => $restaurant->state?->code,
                            'slug' => $restaurant->slug,
                            'is_claimed' => $restaurant->is_claimed,
                        ];

                        $scoreData = [
                            'overall_score' => $score->overall_score,
                            'letter_grade' => $score->letter_grade,
                            'grade_color' => $score->grade_color,
                            'categories' => $score->category_scores,
                            'top_recommendations' => $score->top_recommendations,
                            'all_recommendations' => $score->recommendations ?? [],
                            'percentile' => $score->percentile,
                            'area_rank' => $score->area_rank,
                            'area_total' => $score->area_total,
                            'score_description' => $score->score_description,
                            'is_partial' => false,
                        ];

                        Mail::to($record->email)->queue(new FamerScoreReport(
                            $record,
                            $restaurantData,
                            $scoreData
                        ));

                        $record->markAsSent();

                        Notification::make()
                            ->success()
                            ->title('Email Sent')
                            ->body("Report resent to {$record->email}")
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_emails')
                        ->label('Export Emails')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $emails = $records->pluck('email')->unique()->implode("\n");

                            Notification::make()
                                ->info()
                                ->title('Emails Copied')
                                ->body("Copied {$records->count()} emails to clipboard")
                                ->send();

                            return response()->streamDownload(function () use ($emails) {
                                echo $emails;
                            }, 'famer-leads-' . now()->format('Y-m-d') . '.txt');
                        }),
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
            'index' => Pages\ListFamerScoreRequests::route('/'),
            'view' => Pages\ViewFamerScoreRequest::route('/{record}'),
            'edit' => Pages\EditFamerScoreRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::pending()->count() > 0 ? 'warning' : null;
    }
}
