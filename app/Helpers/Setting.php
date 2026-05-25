<?php 
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class Setting { 

public static function get(string $key, mixed $default = null): mixed {
    return Cache::remember("setting_{$key}", 3600, function() use ($key, $default){
        $row = DB::table('settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    });
}
public static function set(string $key, mixed $value): void {
    DB::table('settings')->updateOrInsert(
        ['key' => $key],
        ['value' => $value, 'updated_at' => now()]
    );

    Cache::forget("setting_{$key}");
}
}