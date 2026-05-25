<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use App\Models\LeaveRequest;

class LeaveRequestsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                                Section::make('Informasi Magang')
                    ->schema([
                        TextEntry::make('intern.intern_code')
                            ->label('Kode Magang'),

                        TextEntry::make('intern.user.name')
                            ->label('Nama Magang'),

                        TextEntry::make('intern.department')
                            ->label('Departemen'),

                        TextEntry::make('intern.position')
                            ->label('Posisi')
                            ->formatStateUsing(fn ($state) =>
                                preg_replace('/([a-z])([A-Z])/', '$1 $2', $state)
                            ),
                    ])
                    ->columns(2),

                Group::make()
                    ->schema([
                        Section::make('Detail Pengajuan')
                            ->schema([
                               TextEntry::make('type')
                                  ->label('Jenis Izin')
                                  ->badge()
                                  ->color(fn ($state) => match ($state) {
                                   'sick' => 'danger',
                                   'annual' => 'success',
                                   'emergency' => 'warning',
                                   'other' => 'gray',
                                                       })
                                  ->formatStateUsing(fn ($state) => match ($state) {
                                     'sick' => 'Sakit',
                                     'annual' => 'Cuti',
                                      'emergency' => 'Darurat',
                                      'other' => 'Lainnya',
                                                      }),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'pending' => 'warning',
                                                       })
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'pending' => 'Pending',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    }),
                                    TextEntry::make('start_date')
                                    ->label('Tanggal Mulai')->date('d F Y'),
                                    TextEntry::make('end_date')
                                    ->label('Tanggal Selesai')->date('d F Y'),
                                    TextEntry::make('total_days')
                                    ->label('Jumlah Hari')
                                    ->state(
                                        fn(LeaveRequest $record) => ($record->start_date->diffInDays($record->end_date) + 1). ' hari'
                                    ),
                                    TextEntry::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->date('d F Y, H:i'),
                                    TextEntry::make('reason')
                                    ->label('Alasan')
                                    ->columnSpanFull(),
                                    TextEntry::make('attachment')
                                    ->label('Lampiran')
                                    ->formatStateUsing(
                                        fn($state) => $state 
                                        ? '<a href="' . asset('storage/'. $state) . '"target="_blank" class="text-blue-600 underline">Lihat Lampiran</a>'
                                        : '-'
                                    )->html()->columnSpanFull(),

                            ])->columns(2),
                            Section::make('Keputusan')
                            ->schema([
                                TextEntry::make('rejection_note')
                                ->label('Alasan Penolakan')
                                ->placeholder('-')
                                ->columnSpanFull()
                            ])->hidden(fn(LeaveRequest $record) => $record->status === 'pending')
                    ]),
            ]);
    }
}
