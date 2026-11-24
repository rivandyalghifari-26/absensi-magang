<?php

namespace App\Filament\Admin\Resources\Attendances\Pages;

use App\Filament\Admin\Resources\Attendances\AttendanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;


class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    public function getTitle(): string|Htmlable 
    {
      return "Detail Presensi - {$this->record->intern->intern_code} - {$this->record->intern->user->name} ({$this->record->date->format('d/m/Y')})";
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->label('Edit Presensi')
            ->color('primary')
            ->icon(Heroicon::OutlinedPencil)
            ->iconSize('xs'),

            Action::make('back')
            ->label('Kembali')
            ->color('gray')
            ->icon(HeroIcon::OutlinedArrowLeft)
            ->iconSize('xs')
            ->url($this->getResource()::getUrl('index')),
        ];
    }
}
