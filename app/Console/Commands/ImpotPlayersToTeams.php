<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImpotPlayersToTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:players_to_teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Players to Story Teams';

    protected $teamPositions = [
        'Inazuma Eleven' => [
            'Hillman' => 'pos-0',
            'Sweet' => 'pos-1',
            'Island' => 'pos-2',
            'Nathaniel' => 'pos-3',
            'Hairtown' => 'pos-4',
            'Butler' => 'pos-5',
            'Gladstone' => 'pos-6',
            'Suffolk' => 'pos-7',
            'Tailor' => 'pos-8',
            'Barista' => 'pos-9',
            'Builder' => 'pos-10',
            'Poe' => 'bench-0',
            'Heart' => 'bench-1',
            'Foreman' => 'bench-2',
            'MacHines' => 'bench-3',
            'Steaky' => 'bench-4',
        ],
        'Inazuma Kids FC' => [
            'Muller' => 'pos-0',
            'Izzy' => 'pos-1',
            'Claus' => 'pos-2',
            'Hall' => 'pos-3',
            'Silver' => 'pos-4',
            'Newman' => 'pos-5',
            'Higgins' => 'pos-6',
            'Riverside' => 'pos-7',
            'Cool' => 'pos-8',
            'Randall' => 'pos-9',
            'Maddie' => 'pos-10',
            'Grantham' => 'bench-0',
            'Karl' => 'bench-1',
            'Plank' => 'bench-2',
            'Cake' => 'bench-3',
            'Grumble' => 'bench-4',
        ],
        'Brain' => [
            'Feldt' => 'pos-0',
            'Stronger' => 'pos-1',
            'Marvel' => 'pos-2',
            'Leading' => 'pos-3',
            'Good' => 'pos-4',
            'Busta' => 'pos-5',
            'Kind' => 'pos-6',
            'Rock' => 'pos-7',
            'Tell' => 'pos-8',
            'Seller' => 'pos-9',
            'Turner' => 'pos-10',
            'Under' => 'bench-0',
            'Stiller' => 'bench-1',
            'Oughtry' => 'bench-2',
            'Mooney' => 'bench-3',
            'Waters' => 'bench-4',
        ],
        'Farm' => [
            'Greeny' => 'pos-0',
            'Work' => 'pos-1',
            'Hillvalley' => 'pos-2',
            'Sherman' => 'pos-3',
            'Hayseed' => 'pos-4',
            'Spray' => 'pos-5',
            'Milky' => 'pos-6',
            'Mother' => 'pos-7',
            'Dawson' => 'pos-8',
            'Roast' => 'pos-9',
            'Muffs' => 'pos-10',
            'Mower' => 'bench-0',
            'Grower' => 'bench-1',
            'Howells' => 'bench-2',
            'Lively' => 'bench-3',
            'Nevis' => 'bench-4',
        ],
        'Kirkwood' => [
            'Neville' => 'pos-0',
            'Night' => 'pos-1',
            'Meenan' => 'pos-2',
            'Mirthful' => 'pos-3',
            'Clover' => 'pos-4',
            'Nashmith' => 'pos-5',
            'Damian' => 'pos-6',
            'Moore' => 'pos-7',
            'Thomas' => 'pos-8',
            'Marvin' => 'pos-9',
            'Tyler' => 'pos-10',
            'Calier' => 'bench-0',
            'Gloom' => 'bench-1',
            'Talis' => 'bench-2',
            'Middleton' => 'bench-3',
            'Wells' => 'bench-4',
        ],
        'Occult' => [
            'Mask' => 'pos-0',
            'Franky' => 'pos-1',
            'Styx' => 'pos-2',
            'Creepy' => 'pos-3',
            'Undead' => 'pos-4',
            'Grave' => 'pos-5',
            'Blood' => 'pos-6',
            'Jiangshi' => 'pos-7',
            'Wolfy' => 'pos-8',
            'Mummy' => 'pos-9',
            'Talisman' => 'pos-10',
            'Zombie' => 'bench-0',
            'Dollman' => 'bench-1',
            'Alien' => 'bench-2',
            'Noir' => 'bench-3',
            'Ghost' => 'bench-4',
        ],
        'Otaku' => [
            'Idol' => 'pos-0',
            'Train' => 'pos-1',
            'Cosplay' => 'pos-2',
            'Robot' => 'pos-3',
            'Hero' => 'pos-4',
            'Novel' => 'pos-5',
            'Custom' => 'pos-6',
            'Arcade' => 'pos-7',
            'Artist' => 'pos-8',
            'Gamer' => 'pos-9',
            'Online' => 'pos-10',
            'Eldorado' => 'bench-0',
            'Signalman' => 'bench-1',
            'Formby' => 'bench-2',
            'Vox' => 'bench-3',
            'Net' => 'bench-4',
        ],
        'Raimon' => [
            'Mark' => 'pos-0',
            'Nathan' => 'pos-1',
            'Jack' => 'pos-2',
            'Bobby' => 'pos-3',
            'Tod' => 'pos-4',
            'Timmy' => 'pos-5',
            'Jude' => 'pos-6',
            'Erik' => 'pos-7',
            'Max' => 'pos-8',
            'Kevin' => 'pos-9',
            'Axel' => 'pos-10',
            'Jim' => 'bench-0',
            'Sam' => 'bench-1',
            'Steve' => 'bench-2',
            'Willy' => 'bench-3',
            'Shadow' => 'bench-4',
        ],
        'Shuriken' => [
            'Hood' => 'pos-0',
            'Hillfort' => 'pos-1',
            'Bandit' => 'pos-2',
            'Thunder' => 'pos-3',
            'Crackshot' => 'pos-4',
            'Cleats' => 'pos-5',
            'Hattori' => 'pos-6',
            'Code' => 'pos-7',
            'Star' => 'pos-8',
            'Samurai' => 'pos-9',
            'Cloak' => 'pos-10',
            'Castle' => 'bench-0',
            'Ronin' => 'bench-1',
            'Hopper' => 'bench-2',
            'Trops' => 'bench-3',
            'Spook' => 'bench-4',
        ],
        'Umbrella' => [
            'Ingram' => 'pos-0',
            'Porter' => 'pos-1',
            'Sefton' => 'pos-2',
            'Chaney' => 'pos-3',
            'Strike' => 'pos-4',
            'Chops' => 'pos-5',
            'Rhymes' => 'pos-6',
            'Morefield' => 'pos-7',
            'Scott' => 'pos-8',
            'Edmonds' => 'pos-9',
            'Cyborg' => 'pos-10',
            'Banker' => 'bench-0',
            'Tunk' => 'bench-1',
            'Most' => 'bench-2',
            'Caperock' => 'bench-3',
            'Molehill' => 'bench-4',
        ],
        'Wild' => [
            'Boar' => 'pos-0',
            'Fishman' => 'pos-1',
            'Lion' => 'pos-2',
            'Toad' => 'pos-3',
            'Chameleon' => 'pos-4',
            'Chicken' => 'pos-5',
            'Eagle' => 'pos-6',
            'Monkey' => 'pos-7',
            'Cheetah' => 'pos-8',
            'Gorilla' => 'pos-9',
            'Snake' => 'pos-10',
            'Bullford' => 'bench-0',
            'Koala' => 'bench-1',
            'Panda' => 'bench-2',
            'Raccoon' => 'bench-3',
            'Mouseman' => 'bench-4',
        ],
        'Zeus' => [
            'Poseidon' => 'pos-0',
            'Apollo' => 'pos-1',
            'Ares' => 'pos-2',
            'Dionysus' => 'pos-3',
            'Hephestus' => 'pos-4',
            'Artemis' => 'pos-5',
            'Hermes' => 'pos-6',
            'Hera' => 'pos-7',
            'Athena' => 'pos-8',
            'Aphrodite' => 'pos-9',
            'Demeter' => 'pos-10',
            'Icarus' => 'bench-0',
            'Achilles' => 'bench-1',
            'Heracles' => 'bench-2',
            'Chronos' => 'bench-3',
            'Medusa' => 'bench-4',
        ],
        'Royal Academy' => [
            'King' => 'pos-0',
            'Master' => 'pos-1',
            'Drent' => 'pos-2',
            'Ingham' => 'pos-3',
            'Simmons' => 'pos-4',
            'Martin' => 'pos-5',
            'Bloom' => 'pos-6',
            'Waldon' => 'pos-7',
            'Swing' => 'pos-8',
            'Hatch' => 'pos-9',
            'Samford' => 'pos-10',
            'Carlton' => 'bench-0',
            'Tomlinson' => 'bench-1',
            'Lawrenson' => 'bench-2',
            'Potts' => 'bench-3',
        ],
    ];

    public function handle()
    {
        $this->info('Starting Players to Story Teams Import...');
        $this->reset();

        $teams = Team::all();

        foreach ($teams as $team) {

            if (!array_key_exists($team->name, $this->teamPositions)) {
                $this->warn("Positions Not Found for: {$team->name}");
                continue;
            }

            $players = Player::where('original_team', $team->name)->get();

            if ($players->isEmpty()) {
                $this->info("Players Not Found for: {$team->name}");
                continue;
            }

            $syncData = [];

            foreach ($players as $player) {

                if (!array_key_exists($player->name, $this->teamPositions[$team->name])) {
                    $this->warn("Position not found for {$player->name} in {$team->name}");
                    continue;
                }

                $positionId = $this->teamPositions[$team->name][$player->name];
                $syncData[$player->id] = ['position_id' => $positionId];
            }

            $team->players()->sync($syncData);
            $this->info(count($syncData) . " Players Synchornized for: {$team->name}");
        }

        $this->info('Players to Story Teams Import finished.');
    }

    protected function reset()
    {
        DB::table('player_team')->truncate();
        DB::statement('ALTER TABLE players AUTO_INCREMENT = 1');

        $this->info('Reseted Tables.');
    }
}
