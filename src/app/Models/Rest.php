<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{

    protected $fillable = [
        'attendance_id',
        'restIn',
        'restOut',
        'restTime',
        'workDate',
    ];

    protected $casts = [
        'restIn' => 'datetime',
        'restOut' => 'datetime',
    ];


    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
