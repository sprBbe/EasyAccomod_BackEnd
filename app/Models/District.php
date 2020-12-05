<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table = "districts";
    /**
     * Get province of districts.
     */
    public function province(){
        return $this->belongsTo('App\Models\Province','id_province');
    }

    /**
     * Get wards of district.
     */
    public function ward(){
        return $this->hasMany('App\Models\Ward','id_district');
    }
}
