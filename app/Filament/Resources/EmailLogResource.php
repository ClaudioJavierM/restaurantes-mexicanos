<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Filament\Resources\EmailLogResource\Widgets\EmailStatsOverview;
use App\Models\EmailLog;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Historial de Emails';
    protected static ?string $modelLabel = 'Email';
    protected static ?string $pluralModelLabel = 'Emails Enviados';
    protected static ?string $navigationGroup = 'Comunicaciones';
    protected static ?int $navigationSort = 1;

    // Only show FAMER emails — exclude SDV/external emails that come in via Resend webhooks with no from_email
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereNotNull('from_email');
    }

    public static function getWidgets(): array
    {
        return [
            EmailStatsOverview::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('to_email')
                ->label('Para')
                ->disabled(),
            TextInput::make('subject')
                ->label('Asunto')
                ->disabled()
                ->columnSpanFull(),
            Placeholder::make('status_display')
                ->label('Estado')
                ->content(fn (EmailLog $record): string => self::getStatusLabel($record->status)),
            Placeholder::make('sent_at_display')
                ->label('Enviado')
                ->content(fn (EmailLog $record): string =>
                    $record->sent_at?->format('d/m/Y H:i:s') ?? 'No enviado'),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Información del Email')
                ->schema([
                    TextEntry::make('to_email')
                        ->label('Para')
                        ->copyable(),
                    TextEntry::make('to_name')
                        ->label('Nombre')
                        ->placeholder('—'),
                    TextEntry::make('from_email')
                        ->label('De')
                        ->placeholder('—'),
                    TextEntry::make('subject')
                        ->label('Asunto')
                        ->weight(FontWeight::Bold)
                        ->columnSpanFull(),
                    TextEntry::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => match($state) {
                            'transactional' => 'Transaccional',
                            'campaign' => 'Campaña',
                            'notification' => 'Notificación',
                            default => $state ?? '—',
                        }),
                    TextEntry::make('category')
                        ->label('Categoría')
                        ->badge()
                        ->placeholder('—'),
                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => self::getStatusLabel($state))
                        ->color(fn (string $state): string => self::getStatusColor($state)),
                    TextEntry::make('sent_at')
                        ->label('Enviado')
                        ->dateTime('d/m/Y H:i:s')
                        ->placeholder('No enviado'),
                ])->columns(2),

            Section::make('Tracking de Engagement')
                ->schema([
                    IconEntry::make('opened_at')
                        ->label('Abierto')
                        ->boolean()
                        ->trueIcon('heroicon-o-eye')
                        ->falseIcon('heroicon-o-eye-slash')
                        ->trueColor('success')
                        ->falseColor('gray'),
                    TextEntry::make('opened_at')
                        ->label('Fecha de apertura')
                        ->dateTime('d/m/Y H:i:s')
                        ->placeholder('No abierto'),
                    IconEntry::make('clicked_at')
                        ->label('Click')
                        ->boolean()
                        ->trueIcon('heroicon-o-cursor-arrow-rays')
                        ->falseIcon('heroicon-o-cursor-arrow-ripple')
                        ->trueColor('primary')
                        ->falseColor('gray'),
                    TextEntry::make('clicked_at')
                        ->label('Fecha de click')
                        ->dateTime('d/m/Y H:i:s')
                        ->placeholder('Sin clicks'),
                ])->columns(2),

            Section::make('Contenido del Email')
                ->schema([
                    TextEntry::make('body_preview')
                        ->label('')
                        ->html()
                        ->columnSpanFull()
                        ->placeholder('Sin vista previa disponible'),
                ])->collapsible(),

            Section::make('Información Técnica')
                ->schema([
                    TextEntry::make('message_id')
                        ->label('Message ID')
                        ->copyable()
                        ->fontFamily('mono')
                        ->placeholder('—'),
                    TextEntry::make('provider')
                        ->label('Proveedor')
                        ->placeholder('—'),
                    TextEntry::make('mailable_class')
                        ->label('Clase Mailable')
                        ->fontFamily('mono')
                        ->placeholder('—'),
                    TextEntry::make('template')
                        ->label('Template')
                        ->placeholder('—'),
                    TextEntry::make('error_message')
                        ->label('Error')
                        ->color('danger')
                        ->placeholder('Sin errores')
                        ->columnSpanFull(),
                ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sent_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Pendiente'),
                TextColumn::make('to_email')
                    ->label('Para')
                    ->searchable()
                    ->copyable()
                    ->limit(25),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn (EmailLog $record): string => $record->subject ?? ''),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'transactional' => 'Transaccional',
                        'campaign' => 'Campaña',
                        'notification' => 'Notificación',
                        default => $state ?? '—',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'transactional' => 'info',
                        'campaign' => 'success',
                        'notification' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::getStatusLabel($state))
                    ->color(fn (string $state): string => self::getStatusColor($state)),
                IconColumn::make('opened_at')
                    ->label('Abierto')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (EmailLog $record): string =>
                        $record->opened_at
                            ? 'Abierto: ' . $record->opened_at->format('d/m/Y H:i')
                            : 'No abierto'),
                IconColumn::make('clicked_at')
                    ->label('Click')
                    ->boolean()
                    ->trueIcon('heroicon-o-cursor-arrow-rays')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->tooltip(fn (EmailLog $record): string =>
                        $record->clicked_at
                            ? 'Click: ' . $record->clicked_at->format('d/m/Y H:i')
                            : 'Sin clicks'),
            ])
            ->defaultSort('sent_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'sent' => 'Enviado',
                        'delivered' => 'Entregado',
                        'opened' => 'Abierto',
                        'clicked' => 'Clickeado',
                        'bounced' => 'Rebotado',
                        'failed' => 'Fallido',
                        'unsubscribed' => 'Desuscrito',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'transactional' => 'Transaccional',
                        'campaign' => 'Campaña',
                        'notification' => 'Notificación',
                    ]),
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'reservation'      => 'Reservación',
                        'claim'            => 'Reclamo',
                        'claim_invitation' => 'Invitación Claim',
                        'famer_email_1'    => 'Email 1 — Intro',
                        'famer_email_2'    => 'Email 2 — Cómo funciona',
                        'famer_email_3'    => 'Email 3 — Recordatorio',
                        'unclaimed_stats'  => 'No Reclamados — Stats',
                        'unclaimed_coupon' => 'No Reclamados — Cupón',
                        'order'            => 'Pedido',
                        'marketing'        => 'Marketing',
                        'team'             => 'Equipo',
                        'verification'     => 'Verificación',
                        'reminder'         => 'Recordatorio',
                        'newsletter'       => 'Newsletter',
                    ]),
                Tables\Filters\Filter::make('opened')
                    ->label('Solo abiertos')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('opened_at')),
                Tables\Filters\Filter::make('clicked')
                    ->label('Con clicks')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('clicked_at')),
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereDate('sent_at', today())),
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta semana')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('sent_at', '>=', now()->startOfWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $failedCount = static::getModel()::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return $failedCount > 0 ? (string) $failedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    private static function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Pendiente',
            'sent' => 'Enviado',
            'delivered' => 'Entregado',
            'opened' => 'Abierto',
            'clicked' => 'Clickeado',
            'bounced' => 'Rebotado',
            'failed' => 'Fallido',
            'unsubscribed' => 'Desuscrito',
            default => $status,
        };
    }

    private static function getStatusColor(string $status): string
    {
        return match ($status) {
            'sent', 'delivered' => 'success',
            'opened' => 'info',
            'clicked' => 'primary',
            'pending' => 'warning',
            'failed', 'bounced' => 'danger',
            'unsubscribed' => 'gray',
            default => 'gray',
        };
    }
}
