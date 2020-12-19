<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all comments of users.
     */
    public function comments(){
        return $this->hasMany('App\Models\Comment','id_from');
    }

    /**
     * Get all notifications of users.
     */
    public function notifications(){
        return $this->hasMany('App\Models\Notification','id_to');
    }

    /**
     * Get role of users.
     */
    public function role(){
        return $this->belongsTo('App\Models\Role','id_role');
    }

    /**
     * Get all post of users.
     */
    public function posts(){
        return $this->hasMany('App\Models\Post','id_owner');
    }

    /**
     * Get ward of users.
     */
    public function ward(){
        return $this->belongsTo('App\Models\Ward','id_ward');
    }

    /**
     * Get favourites of users.
     */
    public function favourites()
    {
        return $this->belongsToMany('App\Models\Post', 'fav_post', 'id_from', 'id_post');
    }
}
