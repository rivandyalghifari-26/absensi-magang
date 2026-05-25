<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Absensi extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $title = 'Absensi Harian';
    protected static ?string $slug = 'absensi';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.absensi';

    public function mount(): void 
    {
        abort_unless(Auth::user()->intern, 403, 'Data magang tidak ditemukan');
    }
    public function getViewData(): array 
    {
        $intern = Auth::user()->intern;
        $attendance = $intern->todayAttendance()->first();

        return [
            'intern' => $intern,
            'attendance' => $attendance,
            'hasCheckedIn' => (bool) $attendance?->check_in,
            'hasCheckedOut' => (bool) $attendance?->check_out,
            'faceDescriptor' => $intern->face_descriptor ? json_decode($intern->face_descriptor) : [],
            'token' => Auth::user()->createToken('absensi')->plainTextToken,
        ];
    }

}
