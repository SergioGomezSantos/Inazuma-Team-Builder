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
            'formations' => Formation::orderByRaw("name = 'Diamante' DESC")->orderBy('name')->get(),
            'emblems' => Emblem::orderByRaw("name = 'Raimon' DESC")->orderBy('name')->get(),
            'coaches' => Coach::orderByRaw("name = 'Seymour Hillman' DESC")->orderBy('name')->get(),
            'players' => Player::orderByRaw("original_team = 'Raimon' DESC")->get()
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
        $extra = [1, 2, 10];
        $regional = [6, 11, 3, 7, 13];
        $futbolFrontier = [9, 4, 5, 12];
        $raimon = [8];

        $allTeamIds = array_merge($extra, $regional, $futbolFrontier, $raimon);

        $teams = Team::with('emblem')
            ->whereIn('id', $allTeamIds)
            ->get();

        $extraTeams = $teams->sortBy(function ($team) use ($extra) {
            return array_search($team->id, $extra);
        })->filter(fn($team) => in_array($team->id, $extra))->values();

        $regionalTeams = $teams->sortBy(function ($team) use ($regional) {
            return array_search($team->id, $regional);
        })->filter(fn($team) => in_array($team->id, $regional))->values();

        $futbolFrontierTeams = $teams->sortBy(function ($team) use ($futbolFrontier) {
            return array_search($team->id, $futbolFrontier);
        })->filter(fn($team) => in_array($team->id, $futbolFrontier))->values();

        $raimonTeam = $teams->filter(fn($team) => in_array($team->id, $raimon))->first();


        return view('teams.story', [
            'extraTeams' => $extraTeams,
            'regionalTeams' => $regionalTeams,
            'futbolFrontierTeams' => $futbolFrontierTeams,
            'raimonTeam' => $raimonTeam
        ]);
    }
}
