<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewLeaveRequests extends ViewRecord
{
    protected static string $resource = LeaveRequestsResource::class;

   

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
             Action::make('back')
            ->label('Kembali')
            ->url($this->getResource()::getUrl('index'))
            ->color('gray')
        ];
    }
}
