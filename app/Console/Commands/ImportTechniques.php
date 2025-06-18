<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Technique;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\DB;

class ImportTechniques extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:techniques';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Techniques and Talents from Wiki';

    protected array $ignoreNames = [
        'Bolas Combinadas',
        'Cabezazo Wallside',
        'Celebración Sanadora',
    ];

    protected $nameCorrections = [
        'CozArrollador' => 'Coz',
        'Coz 3Arrollador 3' => 'Coz 3',
        'Ataque CortanteAtaque Afilado' => 'Ataque Cortante',
        'Ataque OmegaOrden de Tiro 24' => 'Ataque Omega',
        'Campo de Fuerza DefensivoCampo de Fuerza' => 'Campo de Fuerza Defensivo',
        'Fuego HeladoFuego Cruzado' => 'Fuego Helado'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->resetTechniquesTable();

        $this->info('Importing Techniques and Talents...');

        $client = new Client();

        // Technique / Talents
        $urls = [
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Supert%C3%A9cnicas' => 'technique',
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Supert%C3%A9cnicas?from=Defensa+M%C3%BAltiple' => 'technique',
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Supert%C3%A9cnicas?from=La+Liebre' => 'technique',
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Supert%C3%A9cnicas?from=Remate+del+Oso' => 'technique',
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Supert%C3%A9cnicas?from=Ventisca+de+Fuego' => 'technique',
            'https://inazuma.fandom.com/es/wiki/Categor%C3%ADa:Talentos' => 'talent',
        ];


        [$allTechniqueUrls, $modes] = $this->getTechniqueUrls($client, $urls);
        $this->importTechniquesConcurrently($client, $allTechniqueUrls, $modes);

        $this->info('Importing Techniques and Talents Completed');
    }

    /**
     * Elimina técnicas existentes y resetea el AUTO_INCREMENT.
     */
    protected function resetTechniquesTable(): void
    {
        Technique::query()->delete();
        DB::statement('ALTER TABLE techniques AUTO_INCREMENT = 1');
        $this->info('Techniques Table Reseted.');
    }

    /**
     * Return all the categories
     *
     * @param Client $client
     * @param array $urls
     * @return array [array de URLs, array de modos por URL]
     */
    protected function getTechniqueUrls(Client $client, array $urls): array
    {
        $allTechniqueUrls = [];
        $modes = [];

        foreach ($urls as $categoryUrl => $mode) {
            $response = $client->get($categoryUrl);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $techniqueUrls = $crawler->filter('.category-page__member-link')->each(function ($node) use ($mode) {
                $href = $node->attr('href');

                // Ignore Wiki Pages with Errors
                if ($mode === 'talent' && preg_match('#/wiki/Categor%C3%ADa:Talentos_%28IE_\d+\)#', $href)) {
                    return null;
                }

                return 'https://inazuma.fandom.com' . $href;
            });

            $techniqueUrls = array_filter($techniqueUrls);

            foreach ($techniqueUrls as $url) {
                $allTechniqueUrls[] = $url;
                $modes[$url] = $mode;
            }
        }

        return [$allTechniqueUrls, $modes];
    }

    /**
     *
     * @param Client $client
     * @param array $allTechniqueUrls
     * @param array $modes
     */
    protected function importTechniquesConcurrently(Client $client, array $allTechniqueUrls, array $modes): void
    {
        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield new Psr7Request('GET', $url);
            }
        };

        $pool = new Pool($client, $requests($allTechniqueUrls), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use ($allTechniqueUrls, $modes) {
                $url = $allTechniqueUrls[$index];
                $mode = $modes[$url];
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);

                $this->processTechniquePage($crawler, $mode);
            },
            'rejected' => function () {
                // 
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    /**
     *
     * @param Crawler $crawler
     * @param string $mode 'technique' o 'talent'
     */
    protected function processTechniquePage(Crawler $crawler, string $mode): void
    {
        try {
            $name = null;
            $type = null;
            $element = null;

            // Talent
            if ($mode === 'talent') {
                $nameNode = $crawler->filter('.mw-page-title-main');
                if ($nameNode->count() === 0) {
                    return;
                }
                $name = $nameNode->text(null, false);
                $type = 'Talento';
                $element = null;

                // Technique
            } else {
                $nameNode = $crawler->filter('.portable-infobox h2.pi-title');
                if ($nameNode->count() === 0) {
                    return;
                }
                $name = $nameNode->first()->text(null, false);

                // Fix Typo of the Wiki
                $name = $this->nameCorrections[$name] ?? $name;

                $crawler->filter('.pi-data')->each(function ($node) use (&$type, &$element, &$name) {
                    $labelNode = $node->filter('.pi-data-label');
                    $valueNode = $node->filter('.pi-data-value');

                    if ($labelNode->count() && $valueNode->count()) {
                        $label = $labelNode->text(null, false);
                        $value = $valueNode->text(null, false);

                        // Type
                        if (stripos($label, 'Tipo') !== false) {
                            $noParentheses = preg_replace('/\s*\([^)]*\)/', '', $value);
                            $parts = preg_split('/\s+/', trim($noParentheses));
                            $type = end($parts);
                        }

                        // Element
                        if (stripos($label, 'Elemento') !== false) {
                            $noParentheses = preg_replace('/\s*\([^)]*\)/', '', $value);
                            $noBrackets = preg_replace('/\[[^\]]*\]/', '', $noParentheses);
                            $parts = preg_split('/\s+/', trim($noBrackets));
                            $element = end($parts);
                        }
                    }
                });
            }

            // Ignore Empty Errors of the Wiki
            if (empty($type) && empty($element)) {
                return;
            }

            // Ignore Fake Techniques of the Wiki
            if (in_array($name, $this->ignoreNames)) {
                return;
            }

            Technique::updateOrCreate(
                ['name' => $name],
                ['element' => $element, 'type' => $type]
            );

            $this->info("Importing Technique: $name");
        } catch (\Exception $e) {
            //
        }
    }
}
