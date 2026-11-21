<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'treatment_id',
        'bill_date',
        'amount',
        'payment_method',
        'payment_status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
