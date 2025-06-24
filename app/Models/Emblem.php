<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emblem extends Model
{
    protected $fillable = [
        'name',
        'image',
        'version'
    ];
}
