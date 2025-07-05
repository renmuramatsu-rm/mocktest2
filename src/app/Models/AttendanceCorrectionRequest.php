<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{

    protected $table = 'attendance_correction_requests';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clockIn',
        'requested_clockOut',
        'remark',
        'status',
    ];

    protected $casts = [
        'requested_clockIn' => 'datetime',
        'requested_clockOut' => 'datetime',
    ];

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => '承認待ち',
            'approved' => '承認済み',
        };
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
