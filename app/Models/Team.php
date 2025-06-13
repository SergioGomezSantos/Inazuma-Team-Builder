<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function players() {
        return $this->belongsToMany(Player::class);
    }

    public function formation() {
        return $this->belongsTo(Formation::class);
    }

    public function emblem() {
        return $this->belongsTo(Emblem::class);
    }

    public function coach() {
        return $this->belongsTo(Coach::class);
    }

}
