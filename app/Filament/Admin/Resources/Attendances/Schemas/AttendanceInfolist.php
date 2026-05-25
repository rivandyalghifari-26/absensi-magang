<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Icons\Heroicon;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                ->schema([
                    Section::make('Informasi Magang')
                    ->schema([
                        TextEntry::make('intern.intern_code')
                        ->label('Kode Magang'),
                         TextEntry::make('intern.user.name')
                        ->label('Nama Magang'),
                         TextEntry::make('intern.department')
                        ->label('Departemen'),
                         TextEntry::make('intern.position')
                        ->label('Posisi'),
                    ])->columns(2),
                    Section::make('Lokasi Absensi')
                    ->schema([
                        TextEntry::make('check_in_lat')->label('Koordinat Masuk')
                        ->formatStateUsing(
                            fn($state, $record) => $state ? "{$state}, {$record->check_in_lng}" : "-"
                        ),
                        TextEntry::make('check_out_lat')->label('Koordinat Pulang')
                        ->formatStateUsing(
                            fn($state, $record) => $state ? "{$state}, {$record->check_out_lng}" : "-"
                        ),
                    ])->columns(2)
                ]),
                Section::make('Informasi Absensi')
                ->schema([
                    TextEntry::make('date')->label('Tanggal')->date('d/m/Y'),
                    TextEntry::make('status')->label('Status')->badge()
                    ->colors([
                        'primary' => 'it',
                        'success' => 'finance',
                        'danger' => 'hr',
                        'info' => 'operations',
                    ])
                       ->formatStateUsing(fn($state) => match ($state){
                     'present' => 'Hadir',
                     'late' => 'Terlambat',
                     'absent' => 'Tidak Hadir',
                     'leave' => 'Cuti'
                    }),
                    TextEntry::make('check_in')->label('Jam Masuk')->time('H:i')->placeholder('-'),
                    TextEntry::make('check_out')->label('Jam Pulang')->time('H:i')->placeholder('-'),
                    TextEntry::make('check_in_distance')->label('Jarak dari Kantor')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 0) . 'Meter' : '-'),
                    IconEntry::make('face_verified')->label('Verifikasi Wajah')->boolean(),
                    IconEntry::make('is_late')->label('Terlambat')->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('warning'),
                    TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
         
                    
                ])->columns(2)
            ]);
    }
}
