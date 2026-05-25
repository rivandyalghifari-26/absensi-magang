<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeaveRequests extends CreateRecord
{
    protected static string $resource = LeaveRequestsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array 
    {
        $data['intern_id'] = Auth::user()->intern->id;
        return $data;
    }
    public function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }
}
