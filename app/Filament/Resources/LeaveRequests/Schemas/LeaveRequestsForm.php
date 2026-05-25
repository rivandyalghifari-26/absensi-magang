<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;



class LeaveRequestsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                   Group::make()
                ->columnSpanFull()
               ->schema([ 
                Section::make('Data Pengajuan Izin')
                ->schema([
                     

                    Select::make('type')
                            ->label('Jenis Izin')
                            ->options([
                                'sick' => 'Sakit',
                                'annual' => 'Cuti',
                                'emergency' => 'Darurat',
                                'other' => 'Lainnya',
                            ])
                            ->required(),

                    DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) => ($state && $get('end_date') && $state > $get('end_date'))
                            ),

                    DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->minDate(fn($get) => $get('start_date')),

                    Textarea::make('reason')
                            ->label('Alasan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                    FileUpload::make('attachment')
                        ->label('Lampiran (opsional)')
                        ->disk('public')  
                        ->directory('leave-attachment')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->helperText('Unggah dokumen pendukung seperti surat dokter atau yang lainnya.')
                        ->columnSpanFull()
                        ->maxSize(2048) 
                ])->columnSpan(2)->columns(2),

               ])
            ]);
    }
}
