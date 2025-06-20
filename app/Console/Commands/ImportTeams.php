<?php

namespace App\Console\Commands;

use App\Models\Coach;
use App\Models\Emblem;
use App\Models\Formation;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class ImportTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Story Teams, Emblems, Coaches, Formation and Players from Wiki';

    protected $client;
    protected $ignoreNames = [
        'Instituto_Caribbean',
        'Instituto_Empress',
        'Instituto_Shun',
        'J%C3%B3venes_Inazuma',
        'Sallys',
    ];
    protected $exceptionToImageRule = [
        'Mark'
    ];


    protected $baseUrl = 'https://inazuma.fandom.com';
    protected $game = 'IE';
    protected $version = ['Ver._EU', 'Ver. Europea'];

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Story Teams Import...');
        $this->reset();

        $categoryUrl = $this->baseUrl . '/es/wiki/Categor%C3%ADa:Equipos_(IE_Original_T1)';
        $teamUrls = $this->getTeamUrls($categoryUrl);

        foreach ($teamUrls as $teamUrl) {
            $this->info("Importing team: $teamUrl");
            $this->importTeam($teamUrl);
        }

        $this->info('Story Teams Import finished.');
        return 0;
    }

    protected function reset()
    {
        Player::query()->delete();
        DB::statement('ALTER TABLE players AUTO_INCREMENT = 1');

        Emblem::query()->delete();
        DB::statement('ALTER TABLE emblems AUTO_INCREMENT = 1');

        Coach::query()->delete();
        DB::statement('ALTER TABLE coaches AUTO_INCREMENT = 1');

        Formation::query()->delete();
        DB::statement('ALTER TABLE formations AUTO_INCREMENT = 1');

        Team::query()->delete();
        DB::statement('ALTER TABLE teams AUTO_INCREMENT = 1');

        DB::table('player_team')->truncate();
        DB::statement('ALTER TABLE players AUTO_INCREMENT = 1');

        $this->info('Reseted Tables.');
    }

    protected function getTeamUrls(string $categoryUrl): array
    {
        $response = $this->client->get($categoryUrl);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $foundUrls = $crawler->filter('.category-page__member-link')->each(function ($node) {
            return $this->baseUrl . $node->attr('href');
        });

        $teams = [];
        foreach ($foundUrls as $teamUrl) {
            $slug = basename(parse_url($teamUrl, PHP_URL_PATH));
            $ignore = false;
            foreach ($this->ignoreNames as $ignored) {
                if (stripos($slug, $ignored) !== false) {
                    $ignore = true;
                    break;
                }
            }
            if (!$ignore) {
                $teams[] = $teamUrl;
            }
        }

        // Ignore first team to avoid problems with the 3rd game
        return array_values(array_slice($teams, 1));
    }

    protected function importTeam(string $teamUrl): void
    {
        $response = $this->client->get($teamUrl);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Name + Fix typo of thw Wiki
        $teamName = $this->extractTeamName($crawler);

        // Emblem
        $emblemUrl = $this->extractEmblemUrl($crawler);
        $emblem = $emblemUrl ? Emblem::create(['name' => $teamName, 'image' => $emblemUrl]) : null;
        $this->info("Importing Emblem for: " . $teamName);

        // Coach
        [$coachName, $coachImage] = $this->extractCoach($crawler, $teamName);
        $coach = Coach::create(['name' => $coachName, 'image' => $coachImage]);
        $this->info("Importing Coach: " . $coachName);

        // Formation
        [$formationName, $formationLayout] = $this->extractFormation($crawler);
        $formation = Formation::updateOrCreate(
            ['name' => $formationName],
            ['layout' => $formationLayout]
        );
        $this->info("Importing Formation: " . $formationName);

        // User
        $user = User::where('is_admin', true)->first();

        // Team
        Team::create([
            'name' => $teamName,
            'emblem_id' => $emblem ? $emblem->id : null,
            'coach_id' => $coach->id,
            'formation_id' => $formation->id,
            'user_id' => $user->id,
        ]);
        $this->info("Importing Team: " . $teamName);

        // Players of the Team
        $playersTable = $this->getPlayersTable($crawler, $teamName);

        if ($playersTable && $playersTable->count()) {
            $this->importPlayers($playersTable, $teamName);
        }
    }

    protected function extractTeamName(Crawler $crawler): string
    {
        $h2Name = $crawler->filter('.portable-infobox h2.pi-title')->first();
        $teamName = '';
        foreach ($h2Name->getNode(0)->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $teamName = trim($child->nodeValue);
                break;
            }
        }

        // Fix typo of the Wiki
        if ($teamName === 'Inzauma Eleven') {
            $teamName = 'Inazuma Eleven';
        }

        return $teamName;
    }

    protected function extractEmblemUrl(Crawler $crawler): ?string
    {
        $emblemNode = $crawler->filter('figure.mw-halign-center a.image')->last();
        if ($emblemNode->count()) {
            return $emblemNode->attr('href');
        }
        return null;
    }

    protected function extractCoach(Crawler $crawler, string $teamName): array
    {
        $coachName = null;
        $coachImage = null;

        // Special Cases in the Wiki
        if (in_array($teamName, ['Zeus', 'Wild'])) {
            $coachHeader = $crawler->filterXPath("//h3[span[contains(text(), 'Entrenador')]]")->first();
            if ($coachHeader->count()) {
                $coachTable = $coachHeader->nextAll()->filter('table')->first();
                if ($coachTable->count()) {
                    $rows = $coachTable->filter('tbody > tr');
                    if ($rows->count() > 1) {
                        $row = $rows->eq(1);
                        $coachImage = $row->filter('th a img')->first()->attr('data-src') ?? null;
                        $coachName = $row->filter('td')->first()->text(null, false) ?? null;
                        $coachName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $coachName));
                    }
                }
            }

            // Default 
        } else {
            $coachTitle = $crawler->filter('span#Entrenador, span#Entrenadores')->closest('h3');
            $coachTable = $coachTitle->nextAll()->filter('table')->first();

            if ($coachTable->count()) {
                $rows = $coachTable->filter('tbody > tr');
                $rowCount = $rows->count();
                if ($rowCount > 0) {
                    $rowIndex = ($teamName === 'Raimon' && $rowCount > 1) ? $rowCount - 2 : $rowCount - 1;
                    $row = $rows->eq($rowIndex);
                    $coachImage = $row->filter('th a img')->first()->attr('data-src') ?? null;
                    $coachName = $row->filter('td')->first()->text(null, false) ?? null;
                    $coachName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $coachName));
                }
            }
        }

        // Fix typo of the Wiki
        if ($coachName === 'Ray Dark(Solo en el FF)') {
            $coachName = 'Ray Dark';
        }

        return [$coachName, $coachImage];
    }

    protected function extractFormation(Crawler $crawler): array
    {
        $formationNode = $crawler->filter('[data-source="Formación"] .pi-data-value')->first();
        $formationName = null;
        $formationLayout = null;

        // Multiple Options vs 1
        if ($formationNode->count() > 0) {
            if ($formationNode->filter('ul')->count() > 0) {
                $formations = [];
                $formationNode->filter('ul > li')->each(function (Crawler $li) use (&$formations) {
                    $text = trim($li->text(null, false));
                    if (preg_match('/^([^\(]+)\s*\(([\d\-]+)[^)]*\)/', $text, $matches)) {
                        $formations[] = [
                            'name' => trim($matches[1]),
                            'layout' => $matches[2],
                        ];
                    }
                });
                if (!empty($formations)) {
                    $formationName = $formations[0]['name'];
                    $formationLayout = $formations[0]['layout'];
                }
            } else {
                $text = trim($formationNode->text(null, false));
                if (preg_match('/^([^\(]+)\s*\(([\d\-]+)\)/', $text, $matches)) {
                    $formationName = trim($matches[1]);
                    $formationLayout = $matches[2];
                }
            }
        }

        return [$formationName, $formationLayout];
    }

    protected function getPlayersTable(Crawler $crawler, string $teamName): ?Crawler
    {
        if (in_array($teamName, ['Zeus', 'Wild'])) {

            $playersHeader = $crawler->filterXPath("//h2[span[contains(text(),'Miembros')]] | //h3[span[contains(text(), 'Jugadores')]]")->first();

            if ($playersHeader->count()) {
                $table = $playersHeader->nextAll()->filter('table')->first();
                if ($table->count()) {

                    return $table;
                }
            }

            return null;

            // Default 
        } else {

            $playersTitle = $crawler->filter('span#Principales, span#Jugadores')->first()->closest('h3');
            return $playersTitle->nextAll()->filter('table')->first();
        }
    }

    protected function importPlayers(Crawler $table, string $teamName)
    {
        if ($table->count()) {
            $rows = $table->filter('tbody > tr')->slice(1);

            foreach ($rows as $row) {
                $rowCrawler = new Crawler($row);

                // Name
                $playerName = $rowCrawler->filter('th')->eq(1)->filter('a')->last()?->text(null, false);
                if ($playerName === 'Caleb' || $playerName === 'Austin') {
                    continue;
                }

                // Image
                $imageTh = $rowCrawler->filter('th')->eq(1);
                $images = $imageTh->filter('a img');

                if (in_array($playerName, $this->exceptionToImageRule)) {

                    $imageUrl = $images->first()->attr('data-src');
                } else {
                    $imageUrl = $images->count() > 1
                        ? $images->eq(1)->attr('data-src')
                        : $images->first()->attr('data-src');
                }

                // Full Name
                $playerFullname = $rowCrawler->filter('td')->eq(0)->filter('a')->first()?->attr('title');
                $playerUrlName = str_replace(' ', '_', $playerFullname);
                $playerUrl = "https://inazuma.fandom.com/es/wiki/{$playerUrlName}";

                // Position
                $position = $rowCrawler->filter('td')->eq(2)->filter('a')->first()?->attr('title');

                // Element
                $element = $rowCrawler->filter('td')->eq(3)->filter('a')->first()?->attr('title');

                // Stats
                $stats = [];
                $response = $this->client->get($playerUrl);
                $html = $response->getBody()->getContents();
                $playerCrawler = new Crawler($html);


                $statsHeader = $playerCrawler->filter('span#Estadísticas')->closest('h2');
                $tabberDivs = $statsHeader->nextAll()->filter('div.tabber.wds-tabber');

                $selectedContent = null;
                foreach ($tabberDivs as $divNode) {
                    $divCrawler = new Crawler($divNode);
                    $tabs = $divCrawler->filter('ul.wds-tabs > li');

                    // Search Game Index
                    $mainTabIndex = null;
                    $tabs->each(function (Crawler $tab, $i) use (&$mainTabIndex) {
                        if (($tab->attr('data-hash') ?? '') === $this->game) {
                            $mainTabIndex = $i;
                        }
                    });

                    if ($mainTabIndex !== null) {
                        $selectedContent = $divCrawler->filter('div.wds-tab__content')->eq($mainTabIndex);
                        break;
                    }
                }

                // If not Second Option inside Tabber
                if (!$selectedContent && $tabberDivs->count()) {
                    $selectedContent = (new Crawler($tabberDivs->getNode(0)))->filter('div.wds-tab__content')->first();
                }

                // Tabber JP/EU
                $innerTabber = $selectedContent->filter('div.tabber.wds-tabber')->first();

                if ($innerTabber->count()) {

                    // Search for EU Version
                    $innerTabs = $innerTabber->filter('ul.wds-tabs > li');
                    $euTabIndex = null;
                    $innerTabs->each(function (Crawler $tab, $i) use (&$euTabIndex) {

                        $hash = $tab->attr('data-hash') ?? '';
                        if (in_array($hash, $this->version)) {
                            $euTabIndex = $i;
                        }
                    });

                    if ($euTabIndex !== null) {
                        $innerContents = $innerTabber->filter('div.wds-tab__content');
                        $euContent = $innerContents->eq($euTabIndex);
                    } else {

                        // No EU -> first
                        $euContent = $innerTabber->filter('div.wds-tab__content')->first();
                    }
                } else {
                    // No Tabber
                    $euContent = $selectedContent;
                }

                // Parse stats
                $euContent->filter('table')->each(function (Crawler $table) use (&$stats) {
                    $table->filter('tr')->each(function (Crawler $row) use (&$stats) {
                        $labelNode = $row->filter('th')->first();
                        $valueNode = $row->filter('td')->first();
                        if ($labelNode->count() && $valueNode->count()) {
                            $label = trim($labelNode->text(null, false));
                            $value = trim($valueNode->text(null, false));
                            $stats[$label] = $value;
                        }
                    });
                });


                Player::create([
                    'image' => $imageUrl,
                    'name' => $playerName,
                    'full_name' => $playerFullname,
                    'position' => $position,
                    'element' => $element,
                    'original_team' => $teamName,
                    'stats' => $stats
                ]);

                $this->info("Importing Player: " . $playerFullname);
            }
        }
    }
}
