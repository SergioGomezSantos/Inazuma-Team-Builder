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
        return view('teams.index', [
            'teams' => Team::where('user_id', Auth::id())
                ->with(['formation', 'emblem', 'coach'])
                ->orderBy('updated_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teams.form', [
            'formations' => Formation::all(),
            'emblems' => Emblem::all(),
            'coaches' => Coach::all(),
            'players' => Player::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'formation' => 'required|exists:formations,id',
                'emblem' => 'required|exists:emblems,id',
                'coach' => 'required|exists:coaches,id',
                'positions' => 'required|array|min:16',
                'positions.*.positionId' => 'required|string',
                'positions.*.playerId' => 'nullable|exists:players,id',
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
                if ($positionData['playerId'] !== null) {
                    $syncData[$positionData['playerId']] = ['position_id' => $positionData['positionId']];
                }
            }

            $team->players()->sync($syncData);

            if ($request->action === 'data') {
                return redirect()->route('teams.players', ['team' => $team->id]);
            }

            return redirect()->route('teams.index')
                ->with('success', 'Equipo Creado');
        } catch (\Illuminate\Validation\ValidationException $e) {

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->validator->errors()
                ], 422);
            }
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Error al Guardar el Equipo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);

        $team->load(['players' => function ($query) {
            $query->withPivot('position_id');
        }]);

        return view('teams.form', [
            'team' => $team,
            'formations' => Formation::all(),
            'emblems' => Emblem::all(),
            'coaches' => Coach::all(),
            'players' => Player::all(),
            'currentFormation' => $team->formation,
            'currentEmblem' => $team->emblem,
            'currentCoach' => $team->coach,
            'mode' => 'show'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $team->load(['players' => function ($query) {
            $query->withPivot('position_id');
        }]);

        return view('teams.form', [
            'team' => $team,
            'formations' => Formation::all(),
            'emblems' => Emblem::all(),
            'coaches' => Coach::all(),
            'players' => Player::all(),
            'currentFormation' => $team->formation,
            'currentEmblem' => $team->emblem,
            'currentCoach' => $team->coach,
            'mode' => 'edit'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|exists:formations,id',
            'emblem' => 'required|exists:emblems,id',
            'coach' => 'required|exists:coaches,id',
            'positions' => 'required|array|min:16',
            'positions.*.positionId' => 'required|string',
            'positions.*.playerId' => 'nullable|exists:players,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'formation_id' => $validated['formation'],
            'emblem_id' => $validated['emblem'],
            'coach_id' => $validated['coach'],
        ]);

        $syncData = [];
        foreach ($validated['positions'] as $positionData) {
            if ($positionData['playerId'] !== null) {
                $syncData[$positionData['playerId']] = ['position_id' => $positionData['positionId']];
            }
        }

        $team->players()->sync($syncData);

        if ($request->action === 'data') {
            return redirect()->route('teams.players', ['team' => $team->id]);
        }

        return redirect()->route('teams.index')
            ->with('success', 'Equipo Actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Equipo Eliminado');
    }

    /**
     * Display a listing of the Story Teams.
     */
    public function story()
    {

        $main = [1, 15, 34];
        $regional = [2, 3, 4, 5, 6];
        $futbolFrontier = [7, 8, 9, 10];
        $institutosAnime2 = [16, 17, 18, 19, 20, 21];
        $alius = [22, 23, 24, 25, 26, 27, 28, 29];
        $mundial = [35, 36, 37, 38, 39, 40, 41, 42, 43];
        $enemies = [49, 50, 51, 52, 53, 54];
        $extras = [11, 12, 13, 14, 30, 31, 32, 33, 44, 45, 46, 47, 48];

        $allTeamIds = array_merge($main, $regional, $futbolFrontier, $alius, $institutosAnime2, $mundial, $enemies, $extras);

        $teams = Team::with('emblem')
            ->whereIn('id', $allTeamIds)
            ->get();

        $mainTeams = $teams->sortBy(function ($team) use ($main) {
            return array_search($team->id, $main);
        })->filter(fn($team) => in_array($team->id, $main))->values();

        $regionalTeams = $teams->sortBy(function ($team) use ($regional) {
            return array_search($team->id, $regional);
        })->filter(fn($team) => in_array($team->id, $regional))->values();

        $futbolFrontierTeams = $teams->sortBy(function ($team) use ($futbolFrontier) {
            return array_search($team->id, $futbolFrontier);
        })->filter(fn($team) => in_array($team->id, $futbolFrontier))->values();

        $institutosAnime2Teams = $teams->sortBy(function ($team) use ($institutosAnime2) {
            return array_search($team->id, $institutosAnime2);
        })->filter(fn($team) => in_array($team->id, $institutosAnime2))->values();

        $aliusTeams = $teams->sortBy(function ($team) use ($alius) {
            return array_search($team->id, $alius);
        })->filter(fn($team) => in_array($team->id, $alius))->values();

        $mundialTeams = $teams->sortBy(function ($team) use ($mundial) {
            return array_search($team->id, $mundial);
        })->filter(fn($team) => in_array($team->id, $mundial))->values();

        $extrasTeams = $teams->sortBy(function ($team) use ($extras) {
            return array_search($team->id, $extras);
        })->filter(fn($team) => in_array($team->id, $extras))->values();

        $enemiesTeams = $teams->sortBy(function ($team) use ($enemies) {
            return array_search($team->id, $enemies);
        })->filter(fn($team) => in_array($team->id, $enemies))->values();


        return view('teams.story', [
            'mainTeams' => $mainTeams,
            'regionalTeams' => $regionalTeams,
            'futbolFrontierTeams' => $futbolFrontierTeams,
            'institutosAnime2Teams' => $institutosAnime2Teams,
            'aliusTeams' => $aliusTeams,
            'mundialTeams' => $mundialTeams,
            'enemiesTeams' => $enemiesTeams,
            'extrasTeams' => $extrasTeams
        ]);
    }

    /**
     * Display a listing of the Team's Playes Data.
     */
    public function players(Team $team)
    {

        $players = $team->players()->with('stats')->get();


        $posPlayers = $players->filter(function ($player) {
            return strpos($player->pivot->position_id, 'pos-') === 0;
        });

        $benchPlayers = $players->filter(function ($player) {
            return strpos($player->pivot->position_id, 'bench-') === 0;
        });


        $sortedPlayers = $posPlayers->merge($benchPlayers);
        return view('teams.players', [
            'team' => $team,
            'players' => $sortedPlayers
        ]);
    }
}
