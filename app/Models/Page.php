<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    public function posts(){
        return $this->hasMany('App\Models\Post');
    }

    public function photos(){
        return $this->hasMany('App\Models\ArchivePhoto');
    }

    public function videos(){
        return $this->hasMany('App\Models\ArchiveVideo');
    }
}
