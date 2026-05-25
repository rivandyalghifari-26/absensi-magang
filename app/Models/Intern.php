<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Intern extends Model
{
    protected $fillable = [
        'user_id',
        'intern_code',
        'phone',
        'position',
        'department',
        'face_descriptor',
        'status',
    ];

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }


    public function attendances(): HasMany 
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance(): HasOne
    {
        return $this->hasOne(Attendance::class)
        ->whereDate('date', today());
    }

    protected static function booted() 
    {
        static::deleting(function(Intern $intern){
            $intern->user()->delete();
        });
    }

}
