<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportToStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:to-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Placeholders to Storage';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $placeholderPath = resource_path('img/placeholder.png');


        $destinationCoaches = public_path('storage/coaches/placeholder.png');
        $destinationPlayers = public_path('storage/players/placeholder.png');


        if (!File::exists($placeholderPath)) {
            $this->error("Placeholder Not Found in: $placeholderPath");
            return;
        }


        $this->copyPlaceholder($placeholderPath, $destinationCoaches);
        $this->copyPlaceholder($placeholderPath, $destinationPlayers);

        // NUEVO: Copiar carpeta icons completa
        $iconsSourcePath = resource_path('img/icons');
        $iconsDestinationPath = public_path('storage/icons');

        if (!File::exists($iconsSourcePath)) {
            $this->error("Icons folder not found: $iconsSourcePath");
            return;
        }

        $this->copyDirectory($iconsSourcePath, $iconsDestinationPath);

        $this->info('Placeholders and Icons imported');
    }

    protected function copyPlaceholder($source, $destination)
    {

        $destinationDir = dirname($destination);
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0777, true);
        }

        if (File::copy($source, $destination)) {
            $this->info("Placeholder Imported into: $destination");
        } else {
            $this->error("Error Importing Placeholder into: $destination");
        }
    }

    protected function copyDirectory($source, $destination)
    {
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0777, true);
        }

        $items = File::allFiles($source);

        foreach ($items as $item) {
            $relativePath = $item->getRelativePathname();
            $destPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            $destDir = dirname($destPath);
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0777, true);
            }

            File::copy($item->getRealPath(), $destPath);
            $this->info("Icon Imported into: $relativePath");
        }
    }
}
