<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailCampaignResource\Pages;
use App\Models\EmailCampaign;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Campañas de Email';
    protected static ?string $modelLabel = 'Campaña';
    protected static ?string $pluralModelLabel = 'Campañas de Email';
    protected static ?string $navigationGroup = 'Marketing & SEO';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información de la Campaña')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre de la Campaña')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ej: Newsletter Enero 2025')
                        ->columnSpan(2),

                    Forms\Components\Select::make('type')
                        ->label('Tipo')
                        ->options(EmailCampaign::getTypes())
                        ->required()
                        ->default('newsletter'),

                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(EmailCampaign::getStatuses())
                        ->default('draft')
                        ->disabled(fn ($record) => $record && in_array($record->status, ['sending', 'sent'])),
                ])
                ->columns(4),

            Forms\Components\Section::make('Contenido del Email')
                ->schema([
                    Forms\Components\TextInput::make('subject')
                        ->label('Asunto')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ej: ¡Mejora tu FAMER Score hoy!')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('preview_text')
                        ->label('Texto de Vista Previa')
                        ->maxLength(255)
                        ->placeholder('Texto que aparece en la bandeja de entrada...')
                        ->helperText('Aparece después del asunto en la mayoría de clientes de email')
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('content')
                        ->label('Contenido del Email')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'link',
                            'bulletList',
                            'orderedList',
                            'h2',
                            'h3',
                            'blockquote',
                            'codeBlock',
                            'redo',
                            'undo',
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make('merge_tags_help')
                        ->label('Tags Disponibles')
                        ->content(fn () => new \Illuminate\Support\HtmlString(
                            '<div class="text-sm text-gray-500 space-y-1">' .
                            collect(EmailCampaign::getAvailableMergeTags())
                                ->map(fn ($desc, $tag) => "<code class='bg-gray-100 px-1 rounded'>{$tag}</code> - {$desc}")
                                ->join('<br>') .
                            '</div>'
                        ))
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Audiencia')
                ->schema([
                    Forms\Components\Select::make('audience_filter.claimed_status')
                        ->label('Estado de Claim')
                        ->options([
                            'all' => 'Todos los restaurantes',
                            'claimed' => 'Solo restaurantes reclamados',
                            'unclaimed' => 'Solo restaurantes sin reclamar',
                        ])
                        ->default('all'),

                    Forms\Components\Select::make('audience_filter.tier')
                        ->label('Tier')
                        ->options([
                            'all' => 'Todos los tiers',
                            'free' => 'Free',
                            'basic' => 'Basic',
                            'premium' => 'Premium',
                            'elite' => 'Elite',
                        ])
                        ->default('all'),

                    Forms\Components\Select::make('audience_filter.states')
                        ->label('Estados')
                        ->multiple()
                        ->options(fn () => Restaurant::distinct()
                            ->whereNotNull('state')
                            ->pluck('state', 'state')
                            ->sort()
                            ->toArray()
                        )
                        ->placeholder('Todos los estados'),

                    Forms\Components\TextInput::make('audience_filter.min_famer_score')
                        ->label('FAMER Score Mínimo')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->placeholder('0'),

                    Forms\Components\TextInput::make('audience_filter.max_famer_score')
                        ->label('FAMER Score Máximo')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->placeholder('100'),

                    Forms\Components\Toggle::make('audience_filter.has_email')
                        ->label('Solo con email')
                        ->default(true)
                        ->helperText('Solo enviar a restaurantes con email registrado'),
                ])
                ->columns(3)
                ->collapsible(),

            Forms\Components\Section::make('Programación')
                ->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label('Fecha y Hora de Envío')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->minDate(now())
                        ->helperText('Dejar vacío para enviar manualmente'),
                ])
                ->collapsible(),

            Forms\Components\Section::make('Notas')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas Internas')
                        ->placeholder('Notas sobre esta campaña...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EmailCampaign::getTypes()[$state] ?? $state)
                    ->color(fn ($record) => $record->type_color),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EmailCampaign::getStatuses()[$state] ?? $state)
                    ->color(fn ($record) => $record->status_color),

                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Destinatarios')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Enviados')
                    ->numeric()
                    ->alignCenter()
                    ->description(fn ($record) => $record->sent_count > 0
                        ? "{$record->progress_percentage}%"
                        : null),

                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Aperturas')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->color(fn ($state) => match(true) {
                        $state >= 30 => 'success',
                        $state >= 15 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Clicks')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->color(fn ($state) => match(true) {
                        $state >= 5 => 'success',
                        $state >= 2 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Programada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('No programada'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(EmailCampaign::getStatuses()),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(EmailCampaign::getTypes()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) => in_array($record->status, ['draft', 'paused', 'scheduled'])),

                    Tables\Actions\Action::make('send')
                        ->label('Enviar Ahora')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('¿Enviar campaña ahora?')
                        ->modalDescription(fn ($record) => "Esta acción enviará la campaña \"{$record->name}\" a todos los destinatarios.")
                        ->action(function ($record) {
                            // Dispatch job to send campaign
                            dispatch(new \App\Jobs\SendEmailCampaign($record));
                            Notification::make()
                                ->title('Campaña iniciada')
                                ->body('La campaña se está enviando en segundo plano.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => in_array($record->status, ['draft', 'scheduled'])),

                    Tables\Actions\Action::make('pause')
                        ->label('Pausar')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->pause())
                        ->visible(fn ($record) => $record->status === 'sending'),

                    Tables\Actions\Action::make('resume')
                        ->label('Reanudar')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->action(fn ($record) => $record->resume())
                        ->visible(fn ($record) => $record->status === 'paused'),

                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function ($record) {
                            $new = $record->replicate(['status', 'started_at', 'completed_at', 'scheduled_at']);
                            $new->name = $record->name . ' (Copia)';
                            $new->status = 'draft';
                            $new->total_recipients = 0;
                            $new->sent_count = 0;
                            $new->delivered_count = 0;
                            $new->opened_count = 0;
                            $new->clicked_count = 0;
                            $new->bounced_count = 0;
                            $new->failed_count = 0;
                            $new->save();

                            Notification::make()
                                ->title('Campaña duplicada')
                                ->success()
                                ->send();

                            return redirect(static::getUrl('edit', ['record' => $new]));
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => in_array($record->status, ['draft', 'cancelled'])),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn ($records) => $records !== null && $records->count() > 0 ? $records->every(fn ($r) => in_array($r->status, ['draft', 'cancelled'])) : false),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Estadísticas de la Campaña')
                ->schema([
                    Grid::make(6)
                        ->schema([
                            TextEntry::make('total_recipients')
                                ->label('Total Destinatarios')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large),

                            TextEntry::make('sent_count')
                                ->label('Enviados')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large),

                            TextEntry::make('open_rate')
                                ->label('Tasa de Apertura')
                                ->formatStateUsing(fn ($state) => "{$state}%")
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->color(fn ($state) => $state >= 20 ? 'success' : ($state >= 10 ? 'warning' : 'danger')),

                            TextEntry::make('click_rate')
                                ->label('Tasa de Clicks')
                                ->formatStateUsing(fn ($state) => "{$state}%")
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->color(fn ($state) => $state >= 3 ? 'success' : ($state >= 1 ? 'warning' : 'danger')),

                            TextEntry::make('bounce_rate')
                                ->label('Tasa de Rebote')
                                ->formatStateUsing(fn ($state) => "{$state}%")
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->color(fn ($state) => $state <= 2 ? 'success' : ($state <= 5 ? 'warning' : 'danger')),

                            TextEntry::make('failed_count')
                                ->label('Fallidos')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->color(fn ($state) => $state == 0 ? 'success' : 'danger'),
                        ]),
                ])
                ->visible(fn ($record) => $record->status !== 'draft'),

            Section::make('Información General')
                ->schema([
                    TextEntry::make('name')
                        ->label('Nombre'),

                    TextEntry::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EmailCampaign::getTypes()[$state] ?? $state),

                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EmailCampaign::getStatuses()[$state] ?? $state)
                        ->color(fn ($record) => $record->status_color),

                    TextEntry::make('subject')
                        ->label('Asunto')
                        ->columnSpanFull(),

                    TextEntry::make('preview_text')
                        ->label('Texto de Vista Previa')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Contenido')
                ->schema([
                    TextEntry::make('content')
                        ->label('')
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('Tiempos')
                ->schema([
                    TextEntry::make('scheduled_at')
                        ->label('Programada para')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('No programada'),

                    TextEntry::make('started_at')
                        ->label('Iniciada')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('completed_at')
                        ->label('Completada')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('created_at')
                        ->label('Creada')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(4),
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
            'index' => Pages\ListEmailCampaigns::route('/'),
            'create' => Pages\CreateEmailCampaign::route('/create'),
            'view' => Pages\ViewEmailCampaign::route('/{record}'),
            'edit' => Pages\EditEmailCampaign::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutTrashed();
    }
}
