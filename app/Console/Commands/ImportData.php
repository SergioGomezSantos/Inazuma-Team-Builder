<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Player;
use App\Models\Stat;
use App\Models\Coach;
use App\Models\Emblem;
use App\Models\Team;
use App\Models\Formation;
use App\Models\Technique;

class ImportData extends Command
{
    protected $signature = 'import:data';
    protected $description = 'Importa jugadores, stats, coaches, emblems, equipos, posiciones y técnicas desde JSONs';

    public function handle()
    {
        // Crear admin si no existe
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin'],
            [
                'name' => 'admin',
                'password' => bcrypt('1234'),
                'is_admin' => true,
            ]
        );

        // Crear entrenador por defecto
        Coach::firstOrCreate([
            'name' => 'Sin Entrenador',
            'image' => 'placeholder.png',
            'version' => 'all'
        ]);

        $this->info("✅ Usuario admin listo.");

        $basePath = storage_path('/data');
        $versions = ['ie1', 'ie2', 'ie3'];

        // Orden de prioridad por versión
        $versionOrder = [
            'ie1' => [
                'Raimon',
                'Occult',
                'Wild',
                'Brain',
                'Otaku',
                'Royal Academy',
                'Shuriken',
                'Farm',
                'Kirkwood',
                'Zeus',
                'Inazuma Eleven',
                'Inazuma Kids FC',
                'Umbrella',
                'Sallys'
            ],

            'ie2' => [
                'Raimon 2',
                'Alpino',
                'Claustro Sagrado',
                'Royal Academy Redux',
                'Triple C de Osaka',
                'Fauxshore',
                'Mary Times Memorial',
                'Tormenta de Géminis',
                'Épsilon',
                'Épsilon Plus',
                'Prominence',
                'Diamond',
                'Caos',
                'Génesis',
                'Emperadores Oscuros',
                'Jóvenes Inazuma',
                'Servicio Secreto',
                'Mar de Árboles',
                'Robots Guardias'
            ],

            'ie3' => [
                'Inazuma Japón',
                'Big Waves',
                'Leones del Desierto',
                'Dragones de Fuego',
                'Knights of Queen',
                'Los Emperadores',
                'Unicorn',
                'Orfeo',
                'Os Reis',
                'The Little Giants',
                'Caimanes del Cabo',
                'Los Rojos',
                'Brocken Brigade',
                'Grifos de la Rosa',
                'Neo Japón',
                'Equipo D',
                'Zoolan Team',
                'Sky Team',
                'Dark Team',
                'Ángel Oscuro',
                'Equipo Ogro',
            ],
        ];

        foreach ($versions as $version) {
            $versionPath = $basePath . '/' . $version;
            $teamsJsonPath = $versionPath . '/teams.json';

            if (!File::exists($teamsJsonPath)) {
                $this->warn("❌ No se encontró teams.json en $version");
                continue;
            }

            $teamsData = json_decode(File::get($teamsJsonPath), true);

            // Obtener carpetas de equipos
            $teamFolders = collect(File::directories($versionPath));

            // Aplicar orden si existe para la versión
            if (isset($versionOrder[$version])) {
                $priority = $versionOrder[$version];

                $teamFolders = $teamFolders->sortBy(function ($path) use ($priority) {
                    $folderName = basename($path);
                    $index = array_search($folderName, $priority);
                    return $index !== false ? $index : PHP_INT_MAX;
                })->values();
            }

            foreach ($teamFolders as $teamPath) {
                $teamName = basename($teamPath);
                $playerPath = $teamPath . '/players.json';
                $statsPath = $teamPath . '/stats.json';

                $this->info("Importing Team Data for: " . $teamName);

                $teamInfo = collect($teamsData)->firstWhere('team', $teamName);
                if (!$teamInfo) {
                    $this->warn("⚠️ No se encontró entrada para el equipo $teamName en $version");
                    continue;
                }

                // Crear Coach, Emblem y Formation
                $coach = null;
                if (isset($teamInfo['coach'])) {
                    $coach = Coach::firstOrCreate(
                        ['name' => $teamInfo['coach']['name']],
                        [
                            'image' => $teamInfo['coach']['image'],
                            'version' => $version,
                        ]
                    );
                } else {
                    $this->warn("⚠️ El equipo $teamName no tiene Coach definido en $version.");
                    continue;
                }

                $emblem = null;
                if (isset($teamInfo['emblem'])) {
                    $emblem = Emblem::create([
                        'name' => $teamName,
                        'image' => $teamInfo['emblem']['image'] ?? '',
                        'version' => $version,
                    ]);
                } else {
                    $this->warn("⚠️ El equipo $teamName no tiene Emblem definido en $version.");
                    continue;
                }

                // Crear o buscar Formation
                $formationData = $teamInfo['formation'] ?? null;
                if ($formationData) {
                    $formation = Formation::firstOrCreate(
                        ['name' => $formationData['name']],
                        ['layout' => $formationData['layout'] ?? '']
                    );
                } else {
                    $this->warn("⚠️ El equipo $teamName no tiene Formation definido en $version.");
                    continue;
                }

                // Crear el Team
                $team = Team::updateOrCreate(
                    ['name' => $teamName],
                    [
                        'formation_id' => $formation->id,
                        'emblem_id' => $emblem->id,
                        'coach_id' => $coach->id,
                        'user_id' => $adminUser->id,
                    ]
                );

                if (!File::exists($playerPath) || !File::exists($statsPath)) {
                    $this->warn("❌ Faltan player.json o stats.json para $teamName en $version");
                    continue;
                }

                $players = json_decode(File::get($playerPath), true);
                $stats = json_decode(File::get($statsPath), true);

                // Crear jugadores y stats
                foreach ($players as $playerData) {
                    $player = Player::create([
                        'name' => $playerData['name'],
                        'full_name' => $playerData['full_name'],
                        'position' => $playerData['position'],
                        'element' => $playerData['element'],
                        'original_team' => $playerData['original_team'],
                        'image' => $playerData['image'],
                    ]);

                    $statEntry = collect($stats)->firstWhere('player', $playerData['full_name']);

                    if ($statEntry) {
                        foreach (['ie1', 'ie2', 'ie3'] as $v) {
                            if (!empty($statEntry[$v]) && is_array($statEntry[$v])) {
                                $statValues = $statEntry[$v];
                                $statValues['version'] = $v;
                                $statValues['player_id'] = $player->id;
                                Stat::create($statValues);
                            }
                        }
                    }
                }

                // Relacionar players con teams por posición
                $positionsPath = $teamPath . '/player_position.json';
                if (File::exists($positionsPath)) {
                    $positions = json_decode(File::get($positionsPath), true);
                    foreach ($positions as $pos) {
                        $player = Player::where('full_name', $pos['player'])
                            ->where('original_team', $team->name)->first();

                        if ($player) {
                            if (!$team->players()->where('player_id', $player->id)->exists()) {
                                $team->players()->attach($player->id, ['position_id' => $pos['position_id']]);
                            }
                        }
                    }
                }

                // Importar técnicas
                $techniquesPath = $teamPath . '/player_technique.json';
                if (File::exists($techniquesPath)) {
                    $playersTechniques = json_decode(File::get($techniquesPath), true);
                    foreach ($playersTechniques as $playerTech) {
                        $player = Player::where('full_name', $playerTech['player'])
                            ->where('original_team', $team->name)->first();
                        if (!$player) {
                            $this->warn("⚠️ Jugador {$playerTech['player']} no encontrado para técnicas");
                            continue;
                        }

                        foreach ($playerTech as $source => $techs) {
                            if ($source === 'player') continue;

                            foreach ($techs as $techEntry) {
                                if (is_array($techEntry)) {
                                    $techName = $techEntry['name'];
                                    $with = json_encode($techEntry['with'] ?? []);
                                } else {
                                    $techName = $techEntry;
                                    $with = null;
                                }

                                $technique = Technique::where('name', $techName)->first();
                                if (!$technique) {
                                    $this->warn("⚠️ Técnica $techName no encontrada en DB");
                                    continue;
                                }

                                $player->techniques()->attach($technique->id, [
                                    'source' => $source,
                                    'with' => $with,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }


                $this->info("✅ Importado equipo $teamName en $version.");
            }
        }

        $this->info("🏁 Importación completada.");
        return 0;
    }
}
