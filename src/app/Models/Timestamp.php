<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timestamp extends Model
{
    protected $fillable = ['user_id', 'punchIn', 'punchOut'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
