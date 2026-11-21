<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'phone',
        'address',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function bedAdmissions()
    {
        return $this->hasMany(BedAdmission::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
