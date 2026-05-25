<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\LeaveRequest;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('intern.intern_code')
                ->label('Kode Magang')
                ->searchable()
                ->sortable(),
                TextColumn::make('intern.user.name')
                ->label('Nama Magang')
                ->searchable()
                ->sortable(),
                 TextColumn::make('intern.department')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'primary' => 'it',
                        'success' => 'finance',
                        'danger' => 'hr',
                        'info' => 'operations',
                    ])
                   ->formatStateUsing(fn($state) => ucwords(str_replace('-', ' ', $state))),
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
                 SelectFilter::make('type')
                ->label('Jenis')
                ->options([
                                'sick' => 'Sakit',
                                'annual' => 'Cuti',
                                'emergency' => 'Darurat',
                                'other' => 'Lainnya',
                ]),
                Filter::make('pending')
                ->label('Pending Saja')
                ->query(fn($query) => $query->whereStatus('pending')),
                Filter::make('thisMonth')
                ->label('Bulan Ini')
                ->query(fn($query) => $query
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                )->toggle(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
