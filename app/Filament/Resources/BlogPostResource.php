<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blog Posts';

    protected static ?string $navigationGroup = 'Marketing & SEO';

    protected static ?int $navigationSort = 4;

    protected static bool $isLazy = true;

    // ── Form ─────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Tabs::make('Tabs')
                ->tabs([

                    Forms\Components\Tabs\Tab::make('Contenido')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título (ES)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Forms\Components\TextInput::make('title_en')
                                    ->label('Title (EN)')
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug (URL)')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Se genera automáticamente del título. Solo letras, números y guiones.'),

                                Forms\Components\Select::make('author')
                                    ->label('Autor')
                                    ->options([
                                        'Equipo FAMER'     => 'Equipo FAMER',
                                        'Chef Invitado'    => 'Chef Invitado',
                                        'Redacción FAMER'  => 'Redacción FAMER',
                                    ])
                                    ->default('Equipo FAMER')
                                    ->required()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('author')
                                            ->label('Nombre del autor')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(fn (array $data) => $data['author']),
                            ]),

                            Forms\Components\Textarea::make('excerpt')
                                ->label('Extracto (ES)')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Resumen breve para las tarjetas y SEO.'),

                            Forms\Components\Textarea::make('excerpt_en')
                                ->label('Excerpt (EN)')
                                ->rows(3)
                                ->maxLength(500),

                            Forms\Components\RichEditor::make('content')
                                ->label('Contenido (ES)')
                                ->required()
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'h2', 'h3',
                                    'bulletList', 'orderedList', 'blockquote',
                                    'link', 'attachFiles',
                                    'undo', 'redo',
                                ])
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('content_en')
                                ->label('Content (EN)')
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'h2', 'h3',
                                    'bulletList', 'orderedList', 'blockquote',
                                    'link', 'attachFiles',
                                    'undo', 'redo',
                                ])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Imagen y Categoría')
                        ->schema([
                            Forms\Components\FileUpload::make('cover_image')
                                ->label('Imagen de portada')
                                ->image()
                                ->directory('blog/covers')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('16:9')
                                ->imageResizeTargetWidth('1200')
                                ->imageResizeTargetHeight('675')
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('category')
                                    ->label('Categoría')
                                    ->options([
                                        'historia' => 'Historia',
                                        'recetas'  => 'Recetas',
                                        'cultura'  => 'Cultura',
                                        'guias'    => 'Guías',
                                        'chefs'    => 'Chefs',
                                    ])
                                    ->placeholder('Sin categoría'),

                                Forms\Components\TagsInput::make('tags')
                                    ->label('Tags')
                                    ->placeholder('Agregar tag y Enter'),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('SEO & Publicación')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->maxLength(70)
                                ->helperText('Max 70 caracteres. Deja vacío para usar el título principal.'),

                            Forms\Components\Textarea::make('seo_description')
                                ->label('Meta Description')
                                ->rows(3)
                                ->maxLength(160)
                                ->helperText('Max 160 caracteres. Deja vacío para usar el extracto.'),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publicado')
                                    ->default(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $set('published_at', now());
                                        }
                                    }),

                                Forms\Components\Toggle::make('featured')
                                    ->label('Destacado')
                                    ->default(false),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Fecha de publicación')
                                    ->timezone('America/Mexico_City')
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false),
                            ]),
                        ]),

                ])->columnSpanFull(),

        ]);
    }

    // ── Table ────────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->width(60)
                    ->height(40)
                    ->defaultImageUrl(fn () => null),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50)
                    ->weight('semibold'),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'warning'  => 'historia',
                        'success'  => 'recetas',
                        'info'     => 'cultura',
                        'primary'  => 'guias',
                        'danger'   => 'chefs',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'historia' => 'Historia',
                        'recetas'  => 'Recetas',
                        'cultura'  => 'Cultura',
                        'guias'    => 'Guías',
                        'chefs'    => 'Chefs',
                        default    => $state,
                    }),

                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Publicado'),

                Tables\Columns\ToggleColumn::make('featured')
                    ->label('Destacado'),

                Tables\Columns\TextColumn::make('view_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'historia' => 'Historia',
                        'recetas'  => 'Recetas',
                        'cultura'  => 'Cultura',
                        'guias'    => 'Guías',
                        'chefs'    => 'Chefs',
                    ]),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Publicados')
                    ->falseLabel('Borradores'),

                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Destacado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo destacados')
                    ->falseLabel('No destacados'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (BlogPost $record) => url('/blog/' . $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (BlogPost $record) => $record->is_published),

                Tables\Actions\Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (BlogPost $record) {
                        $record->update([
                            'is_published' => true,
                            'published_at' => $record->published_at ?? now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (BlogPost $record) => ! $record->is_published),

                Tables\Actions\Action::make('unpublish')
                    ->label('Despublicar')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->action(fn (BlogPost $record) => $record->update(['is_published' => false]))
                    ->requiresConfirmation()
                    ->visible(fn (BlogPost $record) => $record->is_published),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_published' => true, 'published_at' => now()]))
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Despublicar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ── Pages ────────────────────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
