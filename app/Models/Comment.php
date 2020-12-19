<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table='comments';
    /**
     * Get user of comments.
     */
    public function fromUser(){
        return $this->belongsTo('App\Models\User','id_from');
    }
    /**
     * Get post of comment.
     */
    public function toPost(){
        return $this->belongsTo('App\Models\Post','id_post');
    }
}
