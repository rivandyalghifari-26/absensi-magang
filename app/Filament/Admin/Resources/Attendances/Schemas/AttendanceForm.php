<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Absensi')
                    ->schema([
                        Select::make('intern_id')
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
                            ->preload()
                            ->required(),

                            DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(today()),
                            TimePicker::make('check_in')
                            ->label('Jam Masuk')
                            ->seconds(false),
                            TimePicker::make('check_out')
                            ->label('Jam Keluar')
                            ->seconds(false),
                            Select::make('status')
                            ->label('Status')
                            ->options([
                                'present' => 'Hadir',
                                'late' => 'Terlambat',
                                'absent' => 'Tidak Hadir',
                                'leave' => 'Cuti',
                            ])
                            ->required(),
                            Toggle::make('is_late')
                            ->label('Terlambat')
                            ->inline(false),
                             Toggle::make('face_verified')
                            ->label('Verifikasi Wajah')
                            ->inline(false),

                            Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()

                    ])->columnSpanFull()->columns(2)
            ]);
    }
}