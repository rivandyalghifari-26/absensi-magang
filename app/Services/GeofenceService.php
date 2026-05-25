<?php 

namespace App\Services;
use App\Helpers\Setting;

class GeofenceService
{
    public function calculateDistance(
        float $lat1, float $lon1, float $lat2, float $lon2
    ): float {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

         $c = 2 * atan2(sqrt ($a), sqrt(1 - $a));

         return round($earthRadius * $c, 2);
         
    }
    public function validate(float $internLat, float $internLon): array {
        $officeLat = (float) Setting::get('office_latitude');
        $officeLon = (float) Setting::get('office_longitude');
        $radius = (float) Setting::get('geofence_radius', 50);

        $distance = $this->calculateDistance(
            $internLat, $internLon, $officeLat, $officeLon
        );
        return[
            'allowed' => $distance <= $radius,
            'distance' => $distance,
            'radius' => $radius,
        ];
    }
}