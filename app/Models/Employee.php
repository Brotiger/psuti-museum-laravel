<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function educations(){
        return $this->hasMany('App\Models\Education');
    }

    public function photos(){
        return $this->hasMany('App\Models\Photos');
    }

    public function autobiographys(){
        return $this->hasMany('App\Models\AutobiographyFile');
    }

    public function attainments(){
        return $this->hasMany('App\Models\Attainment');
    }

    public function degrees(){
        return $this->hasMany('App\Models\Degree');
    }

    public function personals(){
        return $this->hasMany('App\Models\PersonalFile');
    }

    public function rewards(){
        return $this->hasMany('App\Models\Reward');
    }

    public function titles(){
        return $this->hasMany('App\Models\Title');
    }

    public function units(){
        return $this->hasMany('App\Models\UnitEmployee');
        //return $this->belongsToMany('App\Models\Unit');
    }

    public function videos(){
        return $this->hasMany('App\Models\Video');
    }
}
