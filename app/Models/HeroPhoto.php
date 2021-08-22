<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroPhoto extends Model
{
    use HasFactory;

    public function hero(){
        return $this->belongsTo('App\Models\Hero', 'hero_id', 'id');
    }
}
