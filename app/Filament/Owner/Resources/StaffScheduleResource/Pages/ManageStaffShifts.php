<?php

namespace App\Filament\Owner\Resources\StaffScheduleResource\Pages;

use App\Filament\Owner\Resources\StaffScheduleResource;
use App\Models\StaffShift;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ManageStaffShifts extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = StaffScheduleResource::class;
    protected static string $view = 'filament.owner.pages.staff-shifts';

    public $staffMember;

    public function mount($record): void
    {
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $this->staffMember = \App\Models\StaffMember::where('restaurant_id', $restaurant->id)
            ->findOrFail($record);
    }

    protected function getTableQuery(): Builder
    {
        return StaffShift::where('staff_member_id', $this->staffMember->id)
            ->orderBy('shift_date', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('shift_date')
                ->label('Fecha')
                ->date('D, d M Y')
                ->sortable(),
            Tables\Columns\TextColumn::make('start_time')
                ->label('Entrada'),
            Tables\Columns\TextColumn::make('end_time')
                ->label('Salida'),
            Tables\Columns\TextColumn::make('duration_hours')
                ->label('Horas')
                ->suffix(' hrs'),
            Tables\Columns\TextColumn::make('status')
                ->label('Estado')
                ->badge()
                ->formatStateUsing(fn (string $state): string =>
                    StaffShift::$statusLabels[$state] ?? ucfirst($state))
                ->color(fn (string $state): string => match($state) {
                    'scheduled' => 'info', 'completed' => 'success',
                    'absent'    => 'danger', 'cancelled' => 'gray',
                    default     => 'gray',
                }),
            Tables\Columns\TextColumn::make('notes')
                ->label('Notas')
                ->placeholder('—'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('mark_completed')
                ->label('Completado')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (StaffShift $r): bool => $r->status === 'scheduled')
                ->action(function (StaffShift $record): void {
                    $record->update(['status' => 'completed']);
                    Notification::make()->title('Turno completado')->success()->send();
                }),
            Tables\Actions\Action::make('mark_absent')
                ->label('Ausente')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (StaffShift $r): bool => $r->status === 'scheduled')
                ->action(function (StaffShift $record): void {
                    $record->update(['status' => 'absent']);
                    Notification::make()->title('Marcado como ausente')->warning()->send();
                }),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('← Volver al Personal')
                ->url(StaffScheduleResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return 'Turnos de ' . $this->staffMember->name;
    }
}
