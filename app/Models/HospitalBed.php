<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalBed extends Model
{
    protected $table = 'hospital_beds';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'week',
        'month',
        'service',
        'available_beds',
        'patients_request',
        'patients_admitted',
        'patients_refused',
    ];
}
