<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Get role of users.
     */
    public function role(){
        return $this->belongsTo('App\Models\Role','id_role');
    }

    /**
     * Get ward of users.
     */
    public function ward(){
        return $this->belongsTo('App\Models\Ward','id_ward');
    }
}
