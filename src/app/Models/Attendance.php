<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'status',
        'workDate',
        'clockIn',
        'clockOut',
        'total_restTime',
        'workTime',
        'remark',
    ];

    protected $casts = [
        'workDate' => 'date',
        'clockIn' => 'datetime',
        'clockOut' => 'datetime',
        'breakIn' => 'datetime',
        'breakOut' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function rests()
    {
        return $this->hasMany(Rest::class, 'attendance_id');
    }
}
