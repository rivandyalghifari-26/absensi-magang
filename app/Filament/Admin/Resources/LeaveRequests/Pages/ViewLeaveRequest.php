<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use App\Models\LeaveRequest;
use Filament\Support\Icons\Heroicon;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;


class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;


    public function getTitle(): string|Htmlable {
        return "Detail Pengajuan Izin - {$this->record->intern->user->name} ({$this->record->start_date->format('d F Y')} - {$this->record->end_date->format('d F Y')})";
    }

   protected function getHeaderActions(): array
{
    return [
       
            Action::make('approve')
            ->label('Setujui')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->iconSize('sm')
            ->color('success')
            ->visible(fn(LeaveRequest $record) => $record->status == 'pending')
            ->requiresConfirmation()
            ->modalHeading('Setujui pengajuan izin?')
            ->modalDescription(fn(LeaveRequest $record) => "Setujui izin 
            {$record->intern->user?->name}" 
           . "({$record->start_date->format('d F Y')} - {$record->end_date->format('d F Y')})"
            )->action(function(LeaveRequest $record) {
                $record->update(['status' => 'approved']);

                $period =CarbonPeriod::create($record->start_date, $record->end_date);
                foreach($period as $date) {
                    Attendance::updateOrCreate([
                        'intern_id' => $record->intern_id,
                        'date' => $date->toDateString()
                    ],[
                        'status' => 'leave'
                    ]);
                }
                Notification::make()->title('Izin disetujui')->success()->send();
            }),
              Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::OutlinedXCircle)
            ->iconSize('sm')
            ->color('danger')
            ->visible(fn(LeaveRequest $record) => $record->status == 'pending')
            ->schema([

            Textarea::make('rejection_note')
            ->label('Alasan penolakan')
            ->rows(3)->required()
            ])->action(function(LeaveRequest $record, array $data){
                $record->update([
                    'status' => 'rejected', 'rejection_note' => $data['rejection_note']
                ]);

                Notification::make()->title('Izin Ditolak')->danger()->send();
            }),

        EditAction::make()
        ->icon(Heroicon::OutlinedPencil)
        ->iconSize('sm')
        ->color('primary'),
        Action::make('back')
            ->label('Kembali')
            ->color('gray')
            ->icon(Heroicon::OutlinedArrowLeft)
            ->iconSize('xs')
            ->url($this->getResource()::getUrl('index')),
    ];
}
}
