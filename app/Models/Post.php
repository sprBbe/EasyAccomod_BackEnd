<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = "posts";

    /**
     * Get all images of posts.
     */
    public function images()
    {
        return $this->hasMany('App\Models\Img', 'id_post');
    }

    /**
     * Get room type of room in post
     */
    public function roomType()
    {
        return $this->belongsTo('App\Models\RoomType', 'id_room_types');
    }

    /**
     * Get ward of room in post
     */
    public function ward()
    {
        return $this->belongsTo('App\Models\Ward', 'id_ward');
    }

    /**
     * Get near place of room in post
     */
    public function nearPlaces()
    {
        return $this->hasMany('App\Models\NearPlace', 'id_post');
    }

    /**
     * Get amenities of room in post
     */
    public function amenities()
    {
        return $this->belongsToMany('App\Models\Amenity', 'amenity_room', 'id_post', 'id_amenity');
    }

    /**
     * Get fav_user of room in post
     */
    public function favUsers()
    {
        return $this->belongsToMany('App\Models\User', 'fav_post', 'id_post', 'id_from');
    }

    /**
     * Get all comments of posts.
     */
    public function comments(){
        return $this->hasMany('App\Models\Comment','id_post');
    }

    /**
     * Get all reports of posts.
     */
    public function reports(){
        return $this->hasMany('App\Models\Report','id_post');
    }
}
