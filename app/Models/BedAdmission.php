<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedAdmission extends Model
{
    protected $table = 'bed_admissions';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'service',
        'bed_week',
        'bed_month',
        'admission_date',
        'discharge_date',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
