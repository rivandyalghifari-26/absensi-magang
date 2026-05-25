<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

     public function getTitle(): string|Htmlable {
        return "Detail Pengajuan Izin - {$this->record->intern->user->name} ({$this->record->start_date->format('d F Y')} - {$this->record->end_date->format('d F Y')})";
    }
   

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
