<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ImportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import All Data from Wiki';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate:rollback');
        $this->call('migrate');

        User::create([
            'name' => 'admin',
            'email' => 'admin@admin',
            'password' => '1234',
            'is_admin' => true
        ]);

        $this->info('| Iniciando importación completa... |');

        $this->call('import:techniques');
        $this->call('import:teams');
        $this->call('import:techniques_to_players');

        $this->info('| Importación completa terminada |');
    }
}
