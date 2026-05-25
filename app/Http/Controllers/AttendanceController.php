<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use App\Helpers\Setting;
use App\Services\GeofenceService;
use App\Services\FonnteService;


class AttendanceController extends Controller
{
    public function __construct(
        private GeofenceService $geofence,
        private FonnteService $fonnte
    ) {}

    public function checkIn(Request $request): JsonResponse
    {
        $intern = Auth::user()->intern;
        //cek apakah karyawan sudah melakukan absensi hari ini

        if ($intern->todayAttendance()->exists()){
            return response()->json([
                'message' => 'Anda sudah melakukan absensi hari ini'
            ], 422);
        }

        //validasi geofence
        $geo = $this->geofence->validate($request->latitude, $request->longitude);

        if(!$geo['allowed']) {
            return response()->json([
                'message' => "Anda berada {$geo['distance']} meter dari kantor. (Jarak maksimal adalah {$geo['radius']} meter)",
                'distance' => $geo['distance'],
                'radius' => $geo['radius'],

            ], 422);
        }
        if($intern->face_descriptor && !$request->face_verified) {
            return response()->json([
                'message' => "Verifikasi wajah gagal, pastikan wajah anda terlihat jelas",
                'distance' => $geo['distance'],
            ], 422);
        }
        $now = now();
        $lateThreshold = Carbon::parse(Setting::get('late_threshold', '08:00:00'));
        $isLate = $now->greaterThan($lateThreshold);

        $attendance = Attendance::create([
            'intern_id' => $intern->id,
             'date' => today(),
             'check_in' => $now->toTimeString(),
             'check_in_lat' => $request->latitude,
             'check_in_lng' => $request->longitude,
             'check_in_distance' => $geo['distance'],
             'is_late' => $isLate,
             'face_verified' => $request->face_verified,
             'status' => $isLate ? 'late' : 'present',
        ]);
        if ($isLate) {
            $this->fonnte->notifyLate($intern->user->name, $now->format('H:i'));
        }

        return response()->json([
            'message' => $isLate ? 'Check-In Berhasil (Terlambat)' : 'Check-In Berhasil',
                'attendance' => $attendance,
                'is_late' => $isLate,
                'distance' => $geo['distance'],

        ]);

    }

     public function checkOut(Request $request): JsonResponse
    {
        $intern = Auth::user()->intern;
       
        $attendance = $intern->todayAttendance()->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'Anda belum check-in hari ini'
            ], 422);
        }
         if ($attendance->check_out) {
            return response()->json([
                'message' => 'Anda sudah melakukan check-out hari ini'
            ], 422);
        }

        //validasi geofence
        $geo = $this->geofence->validate($request->latitude, $request->longitude);

        if(!$geo['allowed']) {
            return response()->json([
                'message' => "Anda berada {$geo['distance']} meter dari kantor. (Jarak maksimal adalah {$geo['radius']} meter)",
                'distance' => $geo['distance'],
                'radius' => $geo['radius'],

            ], 422);
        }
        if($intern->face_descriptor && !$request->face_verified) {
            return response()->json([
                'message' => "Verifikasi wajah gagal, pastikan wajah anda terlihat jelas",
                'distance' => $geo['distance'],
            ], 422);
        }
       

        $attendance->update([
             'check_out' => now()->toTimeString(),
             'check_out_lat' => $request->latitude,
             'check_out_lng' => $request->longitude,

        ]);
       

        return response()->json([
            'message' =>  'Check-Out Berhasil',
                'attendance' => $attendance->fresh(),
                

        ]);

    }
    public function today() {
        $attendance = Auth::user()->intern->todayAttendance()->first();
        return response()->json([
            'has_check_in' => (bool) $attendance->check_in,
            'has_check_out' => (bool) $attendance->check_out,
            'attendance' => $attendance
        ]);
    }

}
