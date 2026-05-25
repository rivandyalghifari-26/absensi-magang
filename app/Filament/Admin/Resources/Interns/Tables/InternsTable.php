<?php

namespace App\Filament\Admin\Resources\Interns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use App\Models\Intern;

class InternsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('intern_code')
                    ->label('Kode Magang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Magang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),

                TextColumn::make('position')
                    ->label('Posisi')
                    ->searchable()
                    ->formatStateUsing(fn($state) => preg_replace('/([a-z])([A-Z])/', '$1 $2', $state)),

                TextColumn::make('department')
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
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Filter Departemen')
                    ->options([
                        'it' => 'IT',
                        'finance' => 'Finance',
                        'hr' => 'HR',
                        'operations' => 'Operations',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('setup-face')
                ->label('Setup Wajah')
                ->icon('heroicon-o-camera')
                ->color('info')
                ->url(fn(Intern $record): string => route('filament.admin.resources.magang.set-face', ['record' => $record]))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}