<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'status',
        'clockIn',
        'clockOut',
        'breakIn',
        'breakOut',
        'breakTime',
        'workTime',
        'remark',
    ];

    protected $casts = [
        'clockIn' => 'datetime',
        'clockOut' => 'datetime',
        'breakIn' => 'datetime',
        'breakOut' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
