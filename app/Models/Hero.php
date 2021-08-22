<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;

    public function videos(){
        return $this->hasMany('App\Models\HeroVideo');
    }

    public function photos(){
        return $this->hasMany('App\Models\HeroPhoto');
    }

    public function rewards(){
        return $this->hasMany('App\Models\HeroReward');
    }

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'addUserId');
    }
}
