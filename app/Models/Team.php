<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'formation_id',
        'emblem_id',
        'coach_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withPivot('position_id')
            // ->orderByRaw("CASE 
            //                 WHEN pivot_position_id LIKE 'pos-%' THEN 0 
            //                 WHEN pivot_position_id LIKE 'bench-%' THEN 1 
            //                 ELSE 2 
            //               END")
            ->withTimestamps();
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function emblem()
    {
        return $this->belongsTo(Emblem::class);
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }
}
