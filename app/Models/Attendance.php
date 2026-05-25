<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Intern;

class Attendance extends Model
{
    protected $fillable = [
    'intern_id',
    'date',
    'check_in',
    'check_out',
    'check_in_lat',
    'check_in_lng',
    'check_out_lat',
    'check_out_lng',
    'check_in_distance',
    'is_late',
    'face_verified',
    'status',
    'notes',

 ];
protected $casts = [
    'date' => 'date',
    'is_late' => 'boolean',
    'face_verified' => 'boolean'
];
public function intern(): BelongsTo 
{
    return $this->belongsTo(Intern::class);
}

}
