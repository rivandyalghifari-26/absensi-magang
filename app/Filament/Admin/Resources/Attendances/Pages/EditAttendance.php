<?php

namespace App\Filament\Admin\Resources\Attendances\Pages;

use App\Filament\Admin\Resources\Attendances\AttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    public function getTitle(): string|Htmlable 
    {
      return "Edit Presensi - {$this->record->intern->intern_code} - {$this->record->intern->user->name} ({$this->record->date->format('d/m/Y')})";
    }


     protected function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
            ->label('Lihat Detail')
            ->color('primary')
            ->icon(Heroicon::OutlinedEye)
            ->iconSize('xs'),

            DeleteAction::make()
            ->label('Hapus Absensi')
            ->color('gray')
            ->icon(Heroicon::OutlinedTrash)
            ->iconSize('xs'),
        ];
    }
}
