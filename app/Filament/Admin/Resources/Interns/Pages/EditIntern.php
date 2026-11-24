<?php

namespace App\Filament\Admin\Resources\Interns\Pages;

use App\Filament\Admin\Resources\Interns\InternResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditIntern extends EditRecord
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
            ->label('Hapus Intern')
            ->color('gray')
            ->icon('heroicon-o-trash')
            ->iconSize('xs'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array {
        $data['user'] = $this->record->user?->toArray() ?? [];
        return $data;
    }

        protected function handleRecordUpdate(Model $record, array $data): Model 
    {
        $userData = $data['user'] ?? [];
        $internData = collect($data)->except('user')->toArray();

       $updateUser = [
        'name' => $userData['name'],
        'email' => $userData['email'],
        'is_active' => $userData['is_active'],
       ];
      if (
    isset($data['password']) &&
    filled($data['password'])
) {
    $updateUser['password'] =
        $updateUser['password'] =
    $data['password'];
}
       $record->user->update($updateUser);
       $record->update($internData);

       return $record;
    }

    protected function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }
}
