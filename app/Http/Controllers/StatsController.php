<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Stat;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $versions = ['ie1', 'ie2', 'ie3'];
        $positions = Player::distinct()->pluck('position')->toArray();
        $elements = Player::distinct()->pluck('element')->toArray();

        $selectedVersion = $request->input('version', 'all');
        $selectedPosition = $request->input('position');
        $selectedElement = $request->input('element');
        $activeStat = $request->input('stat', 'GP');

        $statLabels = [
            'GP' => 'PE',
            'TP' => 'PT',
            'Kick' => 'Tiro',
            'Body' => 'Físico',
            'Control' => 'Control',
            'Guard' => 'Defensa',
            'Speed' => 'Rapidez',
            'Stamina' => 'Aguante',
            'Guts' => 'Valor',
            'Freedom' => 'Libertad',
        ];

        $topPlayers = [];

        foreach ($statLabels as $stat => $name) {

            // Best IDs
            $bestPlayersQuery = Stat::query()
                ->select('player_id')
                ->selectRaw('MAX(' . $stat . ') as max_stat')
                ->when($selectedVersion !== 'all', function ($q) use ($selectedVersion) {
                    $q->where('version', $selectedVersion);
                })
                ->groupBy('player_id');

            if ($selectedPosition) {
                $bestPlayersQuery->whereHas('player', function ($q) use ($selectedPosition) {
                    $q->where('position', $selectedPosition);
                });
            }

            if ($selectedElement) {
                $bestPlayersQuery->whereHas('player', function ($q) use ($selectedElement) {
                    $q->where('element', $selectedElement);
                });
            }

            // Ordered IDs
            $topPlayerIds = $bestPlayersQuery
                ->orderBy('max_stat', 'DESC')
                ->orderBy('player_id')
                ->limit(21)
                ->pluck('player_id');

            // Players by name and original_team
            $players = Player::query()
                ->whereIn('id', $topPlayerIds)
                ->with(['stats' => function ($q) use ($selectedVersion, $stat) {
                    $q->when($selectedVersion !== 'all', function ($q) use ($selectedVersion) {
                        $q->where('version', $selectedVersion);
                    })
                        ->orderBy($stat, 'DESC')
                        ->orderBy('version', 'DESC');
                }])
                ->orderBy('name')
                ->orderBy('original_team')
                ->get()
                ->sortByDesc(function ($player) use ($stat) {
                    return $player->stats->max($stat);
                })
                ->take(21)
                ->map(function ($player) {
                    $player->current_stats = $player->stats->first();
                    return $player;
                });

            $topPlayers[$stat] = $players->values();
        }

        return view('stats.top-players', [
            'statLabels' => $statLabels,
            'topPlayers' => $topPlayers,
            'versions' => $versions,
            'positions' => $positions,
            'elements' => $elements,
            'selectedVersion' => $selectedVersion,
            'selectedPosition' => $selectedPosition,
            'selectedElement' => $selectedElement,
            'activeStat' => $activeStat,
        ]);
    }
}
