<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Emblem;
use App\Models\Formation;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|exists:formations,id',
            'emblem' => 'required|exists:emblems,id',
            'coach' => 'required|exists:coaches,id',
            'positions' => 'required|array|min:16',
            'positions.*.positionId' => 'required|string',
            'positions.*.playerId' => 'required|exists:players,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'formation_id' => $validated['formation'],
            'emblem_id' => $validated['emblem'],
            'coach_id' => $validated['coach'],
            'user_id' => Auth::id() ?? 1,
        ]);

        $syncData = [];
        foreach ($validated['positions'] as $positionData) {
            $syncData[$positionData['playerId']] = ['position_id' => $positionData['positionId']];
        }

        $team->players()->sync($syncData);
        return response()->json(['message' => 'Equipo guardado correctamente', 'team' => $team], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['players' => function ($query) {
            $query->withPivot('position_id');
        }]);

        return view('team-builder', [
            'team' => $team,
            'formations' => Formation::all(),
            'emblems' => Emblem::all(),
            'coaches' => Coach::all(),
            'players' => Player::all(),
            'currentFormation' => $team->formation,
            'currentEmblem' => $team->emblem,
            'currentCoach' => $team->coach
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
    }
}
