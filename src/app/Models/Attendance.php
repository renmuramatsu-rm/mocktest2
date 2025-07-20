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
        'total_restTime' => 'float',
        'workTime' => 'float',
    ];

    public function getFormattedWorkTimeAttribute()
    {
        if ($this->workTime === null) return null;
        $hours = floor($this->workTime);
        $minutes = round(($this->workTime - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getFormattedTotalRestTimeAttribute()
    {
        if ($this->total_restTime === null) return null;
        $hours = floor($this->total_restTime);
        $minutes = round(($this->total_restTime - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function rests()
    {
        return $this->hasMany(Rest::class, 'attendance_id');
    }
}
