<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Technique;

class ImportTechniques extends Command
{
    protected $signature = 'import:techniques';
    protected $description = 'Importa técnicas desde data/techniques.json';

    public function handle()
    {

        $filePath = storage_path('/data/techniques.json');

        if (!File::exists($filePath)) {
            $this->error("❌ No se encontró el archivo techniques.json en storage//data");
            return 1;
        }

        $techniquesData = json_decode(File::get($filePath), true);

        if (!$techniquesData) {
            $this->error("❌ Error al decodificar JSON o archivo vacío.");
            return 1;
        }

        foreach ($techniquesData as $tech) {
            // Validar que tenga los campos mínimos
            if (!isset($tech['name'], $tech['type'])) {
                $this->warn("⚠️ Técnica con datos incompletos omitida.");
                continue;
            }

            // Crear o actualizar técnica por nombre (unique)
            Technique::updateOrCreate(
                ['name' => $tech['name']],
                [
                    'type' => $tech['type'],
                    'element' => $tech['element'] ?? null,
                ]
            );

            $this->info("✅ Técnica '{$tech['name']}' importada.");
        }

        $this->info("🏁 Importación de técnicas completada.");
        return 0;
    }
}
