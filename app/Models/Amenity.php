<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;
    protected $table='amenities';
    /**
     * Get post of room in amenities
     */
    public function posts()
    {
        return $this->belongsToMany('App\Models\Post', 'amenity_room', 'id_amenity', 'id_post');
    }
}
