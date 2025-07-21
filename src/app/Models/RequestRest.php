<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRest extends Model
{
    protected $fillable = [
        'attendance_correction_request_id',
        'workDate',
        'request_restIn',
        'request_restOut',
        'request_restTime'
    ];

    protected $casts = [
        'request_restIn' => 'datetime',
        'request_restOut' => 'datetime',
    ];


    public function attendanceCorrectionRequest()
    {
        return $this->belongsTo(AttendanceCorrectionRequest::class, 'attendance_correction_request_id');
    }
}
