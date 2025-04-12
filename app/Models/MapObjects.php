<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapObjects extends Model
{
    protected $table = "map_objects";
    protected $fillable = [
        'type',
        'data',
    ];
}
