<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $table = 'treatments';

    public $timestamps = false;

    protected $fillable = [
        'appointment_id',
        'category_id',
        'treatment_type',
        'description',
        'cost',
        'treatment_date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function category()
    {
        return $this->belongsTo(TreatmentCategory::class, 'category_id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
