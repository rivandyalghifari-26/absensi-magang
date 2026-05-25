<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
               ->label('Tanggal')
                ->date('d/m/Y')
                ->sortable(),
                TextColumn::make('check_in')
                ->label('Jam Masuk')
                ->time('H:i')
                ->placeholder('-')
                ->color(fn($record) => $record->is_late ? 'warning' : 'success'),
                 TextColumn::make('check_out')
                ->label('Jam Keluar')
                ->time('H:i')
                ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary' => 'present',
                        'warning' => 'late',
                        'danger' => 'absent',
                        'info' => 'leave',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'present' => 'Hadir',
                                'late' => 'Terlambat',
                                'absent' => 'Tidak Hadir',
                                'leave' => 'Cuti',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('Status')
                ->options([
                                'present' => 'Hadir',
                                'late' => 'Terlambat',
                                'absent' => 'Tidak Hadir',
                                'leave' => 'Cuti',
                            ]),
                            
                            
                        Filter::make('range_tanggal')
                        ->label('Rentang tanggal')
                        ->schema([
                            DatePicker::make('from')->label('Dari'),
                            DatePicker::make('until')->label('Sampai'),
                        ])
                        ->query(function($query, array $data){
                            return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                             ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                        })
                        ->indicateUsing(function(array $data): array {
                            $indicators = [];
                            if ($data['from']){
                                $indicators[] = 'Dari: ' . $data['from'];
                            }
                            if ($data['until']){
                                $indicators[] = 'Sampai: ' . $data['until'];
                            }
                            return $indicators;
                        }),
                        Filter::make('today')
                        ->label('Hari ini')
                        ->query(fn($query) => $query->whereDate('date', today()))
                        ->toggle(),
                        Filter::make('late')
                        ->label('Terlambat')
                        ->query(fn($query) => $query->whereIsLate(true))
                        ->toggle(),
            ])
            ->recordUrl(null)
            ->defaultSort('date', 'desc');
    }
}
