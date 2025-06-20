<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\Technique;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class ImportTechniquesToPlayers extends Command
{
    protected $signature = 'import:techniques_to_players';
    protected $description = 'Import Techniques to Players';

    protected $client;
    protected $baseUrl = 'https://inazuma.fandom.com/es/wiki/';

    protected $nameCorrections = [
        'Zig Zag Chispeante' => 'Zig Zag Explosivo',
        'ZigZag Chispeante' => 'Zig Zag Explosivo',
        'Zigzag Chispeante' => 'Zig Zag Explosivo',
        'Cabezazo Kung-fu' => 'Cabezazo Kung-Fu',
        'Ataque Afilado' => 'Ataque Cortante',
        'Robo Rápido' => 'Robo Veloz',
        'Ataque Sombrío' => 'Ataque de las Sombras',
        'Acelerón' => 'Superaceleración',
        'Chut de los 100 toques' => 'Chut de los 100 Toques',
        'Despeje explosivo' => 'Despeje Explosivo',
        'Sismo' => 'Terremoto',
        'Chut Congelante' => 'Remate de Hielo',
        'Muralla infinita' => 'Muralla Infinita',
        'Balón Rodante' => 'Superbalón Rodante',
        'Trama' => 'Trama Trama',
        'Vendimia' => 'Segadora',
        'Niebla venenosa' => 'Niebla Venenosa',
        'Deslizamiento' => 'Deslizamiento de Portería',
        'Corte Flamígero' => 'Entrada de Llamas',
        'Huracan de Bolas de Nieve' => null,
        'Peonza Wallside' => null,
        'Super Trampolín Relámpago' => 'Supertrampolín Relámpago',
        'Cabezazo Wallside' => null,
        'Trueno Saltarín' => null,
        'Patada Kung-fu' => null,
        'Sombra Pelosa' => null,
        'Remate Espiral' => 'Remate en Espiral',
        'Fuego Helado' => 'Fuego Helado',
        'Remate Willy' => 'Remate Gafas',
        'Triangulo Letal' => 'Triángulo Letal',
        'Ataque Cóndor' => 'Ataque de Cóndor',
        'Destrozataladros N2' => null,
        'Muralla Infinita Total' => null,
        'Desde la Temporada 1' => null,
        'Desde la Temporada 2' => null,
        'Desde la Temporada 3' => null,
    ];

    protected $playersWithWikiErrors = [
        "Alan Coe" => [
            'Remate Tarzán',
            'Espejismo de Balón',
            'Balón Rodante',
            'Giro de Mono'
        ],
        "Finn Stoned" => [
            'Pisotón de Sumo',
            'Ataque Sombrío',
            'Bola Falsa',
            'Regate Topo'
        ],
        "Ricky Clover" => [
            'Giro Bobina',
            'Flecha Huracán',
            'Ataque Meteorito',
            'Tornado Inverso'
        ],
        "Joe Small" => [
            'Remolino Cortante',
            'Vendimia',
            'Corte Giratorio',
            'Cuchilla Asesina'
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $this->info('Starting Techniques to Players Import...');
        $this->reset();

        $playerUrls = $this->getPlayerUrls(Player::all());

        foreach ($playerUrls as $playerUrl) {
            $this->info("Importing Techniques to Player: " . $playerUrl['name']);
            $this->importTechniquesToPlayer($playerUrl['id'], $playerUrl['name'], $playerUrl['url']);
        }

        $this->info('Techniques to Players Import finished.');
        return 0;
    }

    protected function reset()
    {
        DB::table('player_technique')->truncate();
        DB::statement('ALTER TABLE players AUTO_INCREMENT = 1');
        $this->info('Player_Technique Table Reset');
    }

    protected function getPlayerUrls(Collection $players): array
    {
        return $players->map(function ($player) {
            return [
                'id' => $player->id,
                'name' => $player->full_name,
                'url' => $this->baseUrl . str_replace(' ', '_', $player->full_name),
            ];
        })->toArray();
    }

    protected function importTechniquesToPlayer(int $playerId, string $playerFullName, string $playerUrl): void
    {
        $response = $this->client->get($playerUrl);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $allTechs = [
            'anime' => $this->getAnimeTechniques($crawler),
            'ie' => $this->getGameTechniques($crawler)
        ];

        if ($playerFullName == 'Alan Coe') {
            $allTechs['anime'] = [];
            $allTechs['ie'] = [];
        }

        if (empty($allTechs['anime']) && empty($allTechs['ie'])) {
            if (isset($this->playersWithWikiErrors[$playerFullName])) {
                $fallbackTechs = $this->playersWithWikiErrors[$playerFullName];
                $allTechs['ie'] = array_map(function ($name) {
                    return ['name' => $name, 'with' => []];
                }, $fallbackTechs);
                $this->warn("Corrected Techniques for $playerFullName");
            } else {
                $this->warn("Techniques Not Found for $playerFullName");
                return;
            }
        }

        $player = Player::find($playerId);
        $existingTechs = Technique::whereIn('name', array_merge(
            array_column($allTechs['anime'], 'name'),
            array_column($allTechs['ie'], 'name')
        ))->get()->keyBy('name');

        foreach ($allTechs as $source => $techniques) {
            if (!is_array($techniques)) continue;

            foreach ($techniques as $tech) {
                $techName = $tech['name'] ?? null;
                if (!$techName) continue;

                $techModel = $this->getTechniqueModel($techName, $existingTechs);
                if (!$techModel) continue;

                $player->techniques()->syncWithoutDetaching([
                    $techModel->id => [
                        'source' => $source,
                        'with' => json_encode($this->extractValidPlayerNames($tech['with'] ?? [])),
                    ],
                ]);
            }
        }
    }

    protected function getTechniqueModel(string $techName, Collection $existingTechs): ?Technique
    {
        if ($techModel = $existingTechs->get($techName)) {
            return $techModel;
        }

        if (isset($this->nameCorrections[$techName])) {
            $correctedName = $this->nameCorrections[$techName];
            if ($correctedName === null) return null;

            $techModel = Technique::where('name', $correctedName)->first();
            if ($techModel) {
                $existingTechs->put($correctedName, $techModel);
                return $techModel;
            }
        }

        $this->warn("Technique Not Found: " . $techName);
        return null;
    }

    protected function getAnimeTechniques(Crawler $crawler): array
    {
        $seasonHeader = $crawler->filterXPath("//p[contains(., 'Temporada 1')]")->first();
        if ($seasonHeader->count() === 0) return [];

        $techniqueList = $seasonHeader->nextAll()->filter('ul')->first();
        if ($techniqueList->count() === 0) return [];

        return $this->extractTechniquesFromList($techniqueList);
    }

    protected function getGameTechniques(Crawler $crawler): array
    {
        $principalesH4 = $crawler->filterXPath("//h4[span[@id='Principales'] or span[contains(text(), 'Principales')]]");
        if ($principalesH4->count() > 0) {
            $tabber = $this->findNextTabberWithLabel($principalesH4->getNode(0), "IE");
            if ($tabber) return $this->extractTechniquesFromTabber(new Crawler($tabber));
        }

        $videojuegosH3 = $crawler->filterXPath("//h3[contains(., 'Videojuegos')] | //h3[span[@id='Videojuegos']]")->last();
        if ($videojuegosH3->count() > 0) {
            $tabber = $this->findNextTabberWithLabel($videojuegosH3->getNode(0), "IE");
            if ($tabber) return $this->extractTechniquesFromTabber(new Crawler($tabber));
        }

        $supertH2 = $crawler->filterXPath("//h2[span[@id='Supertécnicas'] or span[contains(text(), 'Supertécnicas')]]");
        if ($supertH2->count() > 0) {
            $tabber = $this->findNextTabberWithLabel($supertH2->getNode(0), "IE");
            if ($tabber) return $this->extractTechniquesFromTabber(new Crawler($tabber));
        }

        return [];
    }

    protected function findNextTabberWithLabel(\DOMNode $startNode, string $label): ?\DOMNode
    {
        $next = $startNode->nextSibling;
        while ($next !== null) {
            if ($next->nodeType === XML_ELEMENT_NODE) {
                $class = $next->attributes->getNamedItem('class');
                if ($class !== null && preg_match('/tabber|wds-tabber/', $class->nodeValue)) {
                    $tabberCrawler = new Crawler($next);
                    $tabLabels = $tabberCrawler->filter('.wds-tabs__tab-label, .tab-label, .tabbernav > li > a');
                    foreach ($tabLabels as $tabLabel) {
                        if (stripos($tabLabel->textContent, $label) !== false) {
                            return $next;
                        }
                    }
                }
            }
            $next = $next->nextSibling;
        }
        return null;
    }

    protected function extractTechniquesFromList(Crawler $list): array
    {
        $techniques = [];

        $list->filter('li')->each(function (Crawler $li) use (&$techniques) {
            $link = $li->filterXPath('
                .//a[
                    not(contains(@href, "Categoría")) and 
                    not(contains(@href, "wiki/Special:")) and
                    not(ancestor::span[@typeof="mw:File"])
                ]')->first();

            if ($link->count() === 0) return;

            $techName = trim($link->text());
            if (empty($techName)) return;

            $associates = [];
            if (preg_match('/\(Con ([^)]+)\)/i', $li->text(), $matches)) {
                $associates = array_filter(array_map('trim', preg_split('/,| y /i', $matches[1])));
            }

            $techniques[] = [
                'name' => $techName,
                'with' => $associates,
            ];
        });

        return $techniques;
    }

    protected function extractTechniquesFromTabber(Crawler $tabberCrawler): array
    {
        $techniques = [];
        $activeContent = $tabberCrawler->filter('.wds-tab__content.wds-is-current, .tabbertab.active')->first();

        if ($activeContent->count() > 0) {
            $activeContent->filter('ul > li')->each(function (Crawler $li) use (&$techniques) {
                $links = $li->filterXPath('.//a[not(descendant::img)]');
                if ($links->count() === 0) return;

                $techName = trim($links->first()->text());
                if ($techName === '') return;

                $associates = [];
                if (preg_match('/\(Con ([^)]+)\)/i', $li->text(), $matches)) {
                    $associates = array_filter(array_map('trim', preg_split('/,| y /i', $matches[1])));
                }

                $techniques[] = [
                    'name' => $techName,
                    'with' => $associates,
                ];
            });
        }

        return $techniques;
    }

    protected function extractValidPlayerNames(array $rawStrings): array
    {
        $excludeWords = [
            'Supertécnicas',
            'Original',
            'Orion',
            'GO',
            'Chrono',
            'Stones',
            'Galaxy',
            'Unión',
            'de',
            'como',
            'compañeros',
            'y',
            'o',
            'etc'
        ];

        $possibleNames = [];

        foreach ($rawStrings as $string) {
            $tokens = preg_split('/[\s+;\/\[\]\(\)]+/', $string);
            foreach ($tokens as $token) {
                $token = trim($token);
                if ($token !== '' && preg_match('/^\p{Lu}/u', $token) && !in_array($token, $excludeWords, true)) {
                    $possibleNames[] = $token;
                }
            }
        }

        return Player::whereIn('name', array_unique($possibleNames))
            ->orWhereIn('full_name', array_unique($possibleNames))
            ->pluck('name')
            ->toArray();
    }
}
