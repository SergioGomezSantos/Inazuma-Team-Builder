<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technique extends Model
{
    public function players() {
        return $this->belongsToMany(Player::class);
    }

    protected $fillable = [
        'name',
        'element',
        'type'
    ];
}
