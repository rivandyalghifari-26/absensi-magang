<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Icons\Heroicon;
use App\Helpers\Setting;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;

class SettingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan';
    protected static ?string $slug = 'Pengaturan';
    protected static ?int $navigationSort = 2;


    protected string $view = 'filament.admin.pages.setting-page';


    public string $company_name = '';
    public string $office_latitude = '';
    public string $office_longitude = '';
    public string $geofence_radius = '';
    public string $late_threshold = '';
    public string $wa_token = '';
    public string $wa_group_id = '';

    public function mount(): void {
        $this->form->fill([
    'company_name' => Setting::get('company_name', ''),
    'office_latitude' => Setting::get('office_latitude', ''),
    'office_longitude' => Setting::get('office_longitude', ''),
    'geofence_radius' => Setting::get('geofence_radius', '50'),
    'late_threshold' => Setting::get('late_threshold', '08:30'),
    'wa_token' => Setting::get('wa_token', ''),
    'wa_group_id' => Setting::get('wa_group_id', ''),
]);
    }

public function form(Schema $schema): Schema
{
    return $schema->schema([

        Section::make('Informasi Perusahaan')
            ->description('Nama perusahaan yang ditampilkan sistem')
            ->icon('heroicon-o-building-office')
            ->schema([
                TextInput::make('company_name')
                    ->label('Nama Perusahaan')
                    ->required()
                    ->columnSpanFull(),
            ]),

        Section::make('Lokasi & Geofencing')
            ->description('Koordinat kantor digunakan untuk menvalidasi lokasi magang saat presensi')
            ->icon('heroicon-o-map-pin')
            ->schema([
                TextInput::make('office_latitude')
                    ->label('Latitude Kantor')
                    ->required()
                    ->numeric()
                    ->placeholder('-112.768845')
                    ->helperText('Contoh: -112.768845')
                    ->suffixAction(
                        Action::make('open_maps')
                            ->icon('heroicon-o-globe-alt')
                            ->tooltip('Buka Koordinat Maps untuk cari koordinat')
                            ->url('https://www.gps-coordinates.net/', shouldOpenInNewTab: true)
                    ),
                    TextInput::make('office_longitude')
                    ->label('Longitude Kantor')
                    ->required()
                    ->numeric()
                    ->placeholder('112.768845')
                    ->helperText('Contoh: 112.768845'),
                    TextInput::make('geofence_radius')
                    ->label('Radius Geofencing')
                    ->required()
                    ->numeric()
                    ->minValue(10)
                    ->maxValue(1000)
                    ->suffix('meter')
                    ->placeholder('50')
                    ->helperText('Intern harus berada dalam radius ini untuk bisa presensi'),
            ])->columns(2),
       Section::make('Jam Kerja')
            ->description('Peserta magang yang check-in setelah batas ini dianggap terlambat.')
            ->icon('heroicon-o-clock')
            ->schema([
                TimePicker::make('late_threshold')
                    ->label('Batas Jam Masuk')
                    ->required()
                    ->seconds(false)
                    
                    ->helperText('Peserta Magang yang check-in setelah jam ini akan ditandai terlambat')
                   
            ]),
            Section::make('Whatsapp Gateway')
            ->description('Konfigurasi Fonnte untuk notifikasi keterlambatan dan rekap harian')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                TextInput::make('wa_token')
                    ->label('Token Fonnte')
                    ->password()
                    ->revealable()
                    ->helperText('Dapatkan token di https://fonnte.com setelah menghubungkan nomor Whatsapp'),
                    
                    TextInput::make('wa_group_id')
                    ->label('ID Group Whatsapp Target')
                    ->helperText('ID grup WA'),
                    
            ])->columns(2),
    ])->statePath('data');
}
public array $data = [];

public function save(): void {
    $data = $this->form->getState();

    Setting::set('company_name', $data['company_name']);
    Setting::set('office_latitude', $data['office_latitude']);
    Setting::set('office_longitude', $data['office_longitude']);
    Setting::set('geofence_radius', $data['geofence_radius']);
    Setting::set('late_threshold', $data['late_threshold']);
    Setting::set('wa_token', $data['wa_token']);
    Setting::set('wa_group_id', $data['wa_group_id']);

    Notification::make()
    ->title('Pengaturan berhasil disimpan')
    ->success()
    ->send();
}
}