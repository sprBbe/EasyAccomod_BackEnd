<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;
    protected $table = "wards";
    /**
     * Get district of wards.
     */
    public function district(){
        return $this->belongsTo('App\Models\District','id_district');
    }

}
