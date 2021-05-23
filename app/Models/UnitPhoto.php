<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPhoto extends Model
{
    use HasFactory;

    public function unit(){
        return $this->belongsTo('App\Models\Unit', 'unit_id', 'id');
    }
}
