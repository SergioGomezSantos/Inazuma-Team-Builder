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
            $query = Player::query()
                ->select('players.*')
                ->join('stats', function ($join) use ($selectedVersion) {
                    $join->on('players.id', '=', 'stats.player_id');
                    if ($selectedVersion !== 'all') {
                        $join->where('stats.version', $selectedVersion);
                    }
                })
                ->orderBy("stats.{$stat}", 'DESC')
                ->limit(21);

            if ($selectedPosition) {
                $query->where('players.position', $selectedPosition);
            }

            if ($selectedElement) {
                $query->where('players.element', $selectedElement);
            }

            $players = $query->get()->map(function ($player) use ($selectedVersion, $stat) {
                $player->current_stats = $player->stats()
                    ->when($selectedVersion !== 'all', function ($query) use ($selectedVersion) {
                        $query->where('version', $selectedVersion);
                    })
                    ->orderBy($stat, 'DESC')
                    ->first();
                return $player;
            });

            $topPlayers[$stat] = $players;
        }

        return view('stats.top-players', [
            'statLabels' => $statLabels,
            'topPlayers' => $topPlayers,
            'versions' => $versions,
            'positions' => $positions,
            'elements' => $elements,
            'selectedVersion' => $selectedVersion,
            'selectedPosition' => $selectedPosition,
            'selectedElement' => $selectedElement
        ]);
    }
}
