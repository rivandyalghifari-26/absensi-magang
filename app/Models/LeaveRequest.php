<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LeaveRequest extends Model
{
    protected $fillable = [
        'intern_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'attachment',
        'status',
        'rejection_note',

    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function intern(): BelongsTo 
    {
        return $this->belongsTo(Intern::class);
    }
}
