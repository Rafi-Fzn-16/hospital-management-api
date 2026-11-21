<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentCategory extends Model
{
    protected $table = 'treatment_categories';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function treatments()
    {
        return $this->hasMany(Treatment::class, 'category_id');
    }
}
