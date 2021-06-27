<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public function photos(){
        return $this->hasMany('App\Models\EventPhoto');
    }

    public function videos(){
        return $this->hasMany('App\Models\EventVideo');
    }

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'addUserId');
    }
}
