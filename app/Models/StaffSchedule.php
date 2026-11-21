<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    protected $table = 'staff_schedules';

    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'week',
        'present',
        'status',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
