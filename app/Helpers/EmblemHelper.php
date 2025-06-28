<?php

namespace App\Helpers;

class EmblemHelper
{
    public static function getEmblemSize($teamName)
    {
        $sizes = [
            'Mar de Árboles' => 'w-20 h-20',
            'Inazuma Japón' => 'w-20 h-24',
            'Raimon' => 'w-24 h-24',
            'Zeus' => 'w-24 h-24',
            'Umbrella' => 'w-24 h-24',
            'Sallys' => 'w-20 h-20',

            'Raimon 2' => 'w-24 h-24',
            'Servicio Secreto' => 'w-20 h-20',
            'Caos' => 'w-24 h-24',
            'Génesis' => 'w-28 h-28',
            'Robots Guardias' => 'w-24 h-24',
            'Jóvenes Inazuma' => 'w-24 h-24',

            'Leones del Desierto' => 'w-24 h-24',
            'Knights of Queen' => 'w-24 h-24',
            'Unicorn' => 'w-24 h-24',
            'The Little Giants' => 'w-24 h-24',

            'Los Rojos' => 'w-24 h-24',
            'Brocken Brigade' => 'w-24 h-24',
            'Grifos de la Rosa' => 'w-24 h-24',
            'Caimanes del Cabo' => 'w-24 h-24',
            'Equipo Ogro' => 'w-24 h-24',
            'Equipo D' => 'w-28 h-28',
            'Zoolan Team' => 'w-24 h-24',
            'Sky Team' => 'w-24 h-24',
            'Dark Team' => 'w-24 h-24',

            'default' => 'w-32 h-32',
        ];

        return $sizes[$teamName] ?? $sizes['default'];
    }
}
