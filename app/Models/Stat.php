<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = [
        'player_id',
        'version',
        'GP',
        'TP',
        'Kick',
        'Body',
        'Control',
        'Guard',
        'Speed',
        'Stamina',
        'Guts',
        'Freedom'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function getStatsByGameAttribute()
    {
        return $this->stats->groupBy('game');
    }
}
