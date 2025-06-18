<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Formation;
use App\Models\Emblem;
use App\Models\Coach;

class TeamBuilderController extends Controller
{
    public function index()
    {
        return view('team-builder', [
            'formations' => Formation::orderByRaw("name = 'Diamante' DESC")->orderBy('name')->get(),
            'emblems' => Emblem::orderByRaw("name = 'Raimon' DESC")->orderBy('name')->get(),
            'coaches' => Coach::orderByRaw("name = 'Seymour Hillman' DESC")->orderBy('name')->get(),
            'players' => Player::orderByRaw("original_team = 'Raimon' DESC")->get()
        ]);
    }
}
