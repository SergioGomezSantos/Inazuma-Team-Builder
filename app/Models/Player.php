<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $casts = [
        'stats' => 'array',
    ];

    protected $fillable = [
        'name',
        'full_name',
        'position',
        'element',
        'original_team',
        'stats',
        'image'
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function techniques()
    {
        return $this->belongsToMany(Technique::class)
            ->withPivot(['with', 'source']);
    }
}
