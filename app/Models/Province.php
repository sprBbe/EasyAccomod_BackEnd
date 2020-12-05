<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = "provinces";
    /**
     * Get districts of province.
     */
    public function district(){
        return $this->hasMany('App\Models\District','id_province');
    }
}
