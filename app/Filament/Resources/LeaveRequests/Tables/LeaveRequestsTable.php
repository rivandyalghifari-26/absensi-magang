<?php

namespace App\Filament\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;


use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\LeaveRequest;
use Filament\Tables\Filters\SelectFilter;


class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('intern.user.name')
                ->label('Nama Magang')
                ->searchable()
                ->sortable(),
                TextColumn::make('type')
                  ->label('Jenis Izin')
                 ->badge()
                 ->colors([
                  'danger' => 'sick',
                  'success' => 'annual',
                  'warning' => 'emergency',
                  'gray' => 'other',
    ])
    ->formatStateUsing(fn ($state) => match ($state) {
        'sick' => 'Sakit',
        'annual' => 'Cuti',
        'emergency' => 'Darurat',
        'other' => 'Lainnya',
    }),
                TextColumn::make('start_date')
               ->label('Mulai')
                ->date('d/m/Y')
                ->sortable(),
                TextColumn::make('end_date')
               ->label('Sampai')
                ->date('d/m/Y'),
                TextColumn::make('total_days')
                ->label('Hari')
                ->state(
                    fn(LeaveRequest $record) => ($record->start_date->diffInDays($record->end_date) + 1) . ' Hari'
                ),
               TextColumn::make('status')
                 ->label('Status')
                 ->badge()
                 ->colors([
                   'success' => 'approved',
                   'danger' => 'rejected',
                 'warning' => 'pending',
                                  ])
    ->formatStateUsing(fn ($state) => match ($state) {
        'pending' => 'Pending',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    }),
            ])
            ->filters([
                 SelectFilter::make('status')
                ->label('Status')
                ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                
            ]);

    }
}
