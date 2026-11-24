<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;



class AttendancesTable
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
              ->headerActions([
                ExportAction::make()
                    ->label('Export Excel')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'presensi-magang-' . date('Y-m-d')),
                    ]),
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
                            SelectFilter::make('intern_id')
                            ->label('Magang')
                            ->relationship(
                                'intern',
                                'intern_code',
                                fn ($query) => $query->with('user')->whereStatus('active')
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->intern_code} - {$record->user->name}"
                            )
                            ->searchable()
                            ->preload(),
                            SelectFilter::make('department')
                            ->label('Departemen')
                            ->options([
                            'it' => 'IT',
                            'finance' => 'Finance',
                            'hr' => 'HR',
                            'operations' => 'Operations',
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped();
    }
}
