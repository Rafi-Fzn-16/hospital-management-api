<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'role',
        'department',
        'phone',
    ];

    public function schedules()
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id', 'id');
    }

    public function staffSchedules()
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id', 'id');
    }
}