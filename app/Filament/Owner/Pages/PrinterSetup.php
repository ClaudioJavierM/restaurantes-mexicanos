<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;

class PrinterSetup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Impresora';
    protected static ?string $title = 'Impresora de Cocina';
    protected static ?string $navigationGroup = 'Pedidos';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.owner.pages.printer-setup';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }
}
