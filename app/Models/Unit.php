<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    public function photos(){
        return $this->hasMany('App\Models\UnitPhoto');
    }

    public function videos(){
        return $this->hasMany('App\Models\UnitVideo');
    }
}
