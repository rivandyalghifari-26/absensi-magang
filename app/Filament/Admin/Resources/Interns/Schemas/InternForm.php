<?php

namespace App\Filament\Admin\Resources\Interns\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class InternForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    Section::make('Data Magang')
                    ->description('Informasi dasar')
                    ->schema([
                        TextInput::make('user.name')
                        ->label('Nama Magang')
                        ->required(),
                        TextInput::make('intern_code')
                        ->label('Kode Magang')
                        ->unique(ignoreRecord: true)
                        ->placeholder('MA-001')
                        ->required(),

                        Select::make('department')
                        ->label('Departemen')
                        ->required()
                        ->options([
                            'it' => 'IT',
                            'finance' => 'Finance',
                            'hr' => 'HR',
                            'operations' => 'Operations',
                        ]),
                        TextInput::make('position')
                        ->label('Posisi')
                        ->required(),
                        TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->required(),
                        Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'active' => 'Aktif',
                            'inactive' => 'Tidak Aktif'
                        ]),
                    ])->columns(2)->columnSpan(2),

                    Section::make('Akun Login')
                    ->description('Informasi yang digunakan untuk login')
                    ->schema([
                        TextInput::make('user.email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(
                           table : 'users',
                           column : 'email',
                           ignoreRecord: true,
                           modifyRuleUsing : function($rule, $record){
                               return $record ? $rule->ignore($record->user_id, 'id')
                               : $rule;

                           }
                        ),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(fn(string $context) => $context === 'create')
                        ->dehydrated(fn($state) => filled($state))
                        ->helperText('Kosongkan jika tidak ingin mengubah password'),

                        Toggle::make('user.is_active')
                        ->label('Akun Aktif')
                        ->default(true)
                    ]),
                ])
            ]);
    }
}
