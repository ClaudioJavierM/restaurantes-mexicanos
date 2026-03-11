<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use App\Models\ReviewReport;
use App\Notifications\ReviewApprovedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $pending = Review::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Review Details')->schema([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('name')->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\Select::make('rating')
                    ->options([1 => '1 ⭐', 2 => '2 ⭐', 3 => '3 ⭐', 4 => '4 ⭐', 5 => '5 ⭐'])
                    ->required(),
                Forms\Components\TextInput::make('title')->maxLength(200),
                Forms\Components\Textarea::make('comment')->rows(4)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Moderation')->schema([
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->required(),
                Forms\Components\Toggle::make('is_active')->label('Active'),
                Forms\Components\Toggle::make('flagged_suspicious')->label('Flag as Suspicious'),
                Forms\Components\TextInput::make('trust_score')
                    ->numeric()->minValue(0)->maxValue(100),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->sortable()->searchable()->limit(25),
                Tables\Columns\TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->searchable(['name', 'guest_name'])
                    ->getStateUsing(fn (Review $r) => $r->reviewer_name),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (int $state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(60)->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('trust_score')
                    ->label('Trust')
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state >= 70 => 'success',
                        $state >= 40 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')->boolean()->label('Verified'),
                Tables\Columns\IconColumn::make('flagged_suspicious')
                    ->boolean()->label('Suspicious')
                    ->trueColor('danger')->falseColor('gray'),
                Tables\Columns\TextColumn::make('reports_count')
                    ->label('Reports')
                    ->counts('reports')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\TernaryFilter::make('flagged_suspicious')->label('Suspicious'),
                Tables\Filters\TernaryFilter::make('is_verified')->label('Verified'),
                Tables\Filters\Filter::make('has_reports')
                    ->label('Has Reports')
                    ->query(fn (Builder $q) => $q->has('reports')),
                Tables\Filters\Filter::make('pending_reports')
                    ->label('Pending Reports')
                    ->query(fn (Builder $q) => $q->whereHas('reports', fn ($q) => $q->where('status', 'pending'))),
            ])
            ->actions([
                // Approve
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $r) => $r->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update([
                            'status'      => 'approved',
                            'is_active'   => true,
                            'approved_at' => now(),
                        ]);
                        $record->restaurant->updateRating();
                        cache()->forget("review_alerts_{$record->restaurant_id}");

                        if ($record->user) {
                            $record->user->notify(new ReviewApprovedNotification($record));
                        }

                        Notification::make()->success()
                            ->title('Review Approved')
                            ->send();
                    }),

                // Reject
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Review $r) => $r->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update(['status' => 'rejected', 'is_active' => false]);
                        $record->restaurant->updateRating();
                        cache()->forget("review_alerts_{$record->restaurant_id}");

                        Notification::make()->warning()
                            ->title('Review Rejected')
                            ->send();
                    }),

                // Flag suspicious
                Tables\Actions\Action::make('flag')
                    ->label('Flag')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->visible(fn (Review $r) => !$r->flagged_suspicious)
                    ->action(function (Review $record) {
                        $record->update(['flagged_suspicious' => true]);
                        Notification::make()->warning()->title('Review Flagged')->send();
                    }),

                // View reports
                Tables\Actions\Action::make('view_reports')
                    ->label('Reports')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn (Review $r) => $r->reports()->pending()->exists())
                    ->modalHeading(fn (Review $r) => "Reports for Review #{$r->id}")
                    ->modalContent(fn (Review $record) => view(
                        'filament.review-reports-modal',
                        ['reports' => $record->reports()->with('user')->get(), 'review' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'approved', 'is_active' => true, 'approved_at' => now()]);
                                $record->restaurant->updateRating();
                                cache()->forget("review_alerts_{$record->restaurant_id}");
                            }
                            Notification::make()->success()->title('Reviews Approved')->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'rejected', 'is_active' => false]);
                                $record->restaurant->updateRating();
                                cache()->forget("review_alerts_{$record->restaurant_id}");
                            }
                            Notification::make()->warning()->title('Reviews Rejected')->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit'   => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
