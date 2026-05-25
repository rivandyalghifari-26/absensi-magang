<?php

namespace App\Services;
use App\Helpers\Setting;
use Illuminate\Support\Facades\Log;
class FonnteService
{

private string $baseUrl = 'https://api.fonnte.com';

public function send(string $target, string $message):bool {
    $token = Setting::get('wa_token');

    if(empty($token)){
        Log::warning('FonnteService: WA token not set');
        return false;
    }
    $response = Http::withHeaders([
        'Authorization' => $token,

    ])->post("{$this->baseUrl}/send", [
        'target' => $target,
        'message' => $message
    ]);

    if(!$response->succesful()){
        Log::error('FonnteService: Failed to send message',[
            'response' => $response->body()
        ]);
        return false;
    }
    return true;
}
public function notifylate(string $internName, string $checkInTime): bool
{
    $groupId = Setting::get('wa_group_id');
    $lateThreshold = Setting::get('late_threshold', '09:00');

    if(empty($groupId)){
      Log::warning('FonnteService: WA GROUP ID not set');
        return false;  
    }
    $message = "*NOTIFIKASI KETERLAMBATAN*\n\n"
    . " Intern : *{$internName}*\n"
    . "Check-in : *{$checkInTime}*\n"
    . "Tanggal : *" . now()->translatedFormat('d F Y') . "*\n\n"
    . "_Pesan otomatis dari Sistem Absensi_";
    return $this->send($groupId, $message);

}
public function sendDailySummary(int $total, int $present, int $late, int $absent):bool 
{
    $groupId = Setting::get('wa_group_id');


    if(empty($groupId)){
      Log::warning('FonnteService: WA GROUP ID not set');
        return false;  
    }
    $message = "*REKAP ABSENSI HARIAN*\n"
    . str_repeat('-', 25) . "\n"
    . now()->translatedFormat('d F Y') . "\n"
    . "Hadir          : *{$present}* orang\n"
    . "Terlambat      : *{$late}* orang\n"
    . "Absen          : *{$absent}* orang\n"
    . str_repeat('-', 25) . "\n"
    . "_Sistem Absensi Otomatis_";

    return $this->send($groupId, $message);
}
}