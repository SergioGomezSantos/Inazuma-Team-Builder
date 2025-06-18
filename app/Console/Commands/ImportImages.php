<?php

namespace App\Console\Commands;

use App\Models\Coach;
use App\Models\Emblem;
use App\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportImages extends Command
{
    protected $signature = 'import:images {--force : Sobreescribir imágenes existentes}';
    protected $description = 'Import Images from Wiki. Use only with after import:teams / import:all';

    public function handle()
    {
        $this->info('Importing Images...');

        Storage::disk('public')->makeDirectory('players');
        Storage::disk('public')->makeDirectory('emblems');
        Storage::disk('public')->makeDirectory('coaches');

        $this->processModels(Player::all(), 'players', 'full_name');
        $this->processModels(Emblem::all(), 'emblems', 'name');
        $this->processModels(Coach::all(), 'coaches', 'name');

        $this->info('Import Images Completed');
    }

    protected function processModels($models, $folder, $nameAttribute)
    {
        foreach ($models as $model) {
            try {

                $this->info("Importing Image for: {$model->name}");                
                $originalUrl = $model->image;

                $filename = Str::slug($model->{$nameAttribute}) . '.png';
                $path = "{$folder}/{$filename}";

                // --force
                if (!$this->option('force') && Storage::disk('public')->exists($path)) {
                    $this->info("Image Already Exists for: {$model->name}");   
                    continue;
                }

                $imageContent = @file_get_contents($originalUrl);
                if ($imageContent === false) {
                    $this->warn("Importing Image Failed for {$model->{$nameAttribute}}");
                    continue;
                }

                Storage::disk('public')->put($path, $imageContent);
                $model->update(['image' => $filename]);

            } catch (\Exception $e) {
                $this->warn("Importing Image Error for {$model->{$nameAttribute}}: " . $e->getMessage());
            }
        }
    }
}