export const SPECIAL_FORMATIONS = {
    Diamante: {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (4)
            { x: 20, y: 60, role: "Defensa" },
            { x: 40, y: 70, role: "Defensa" },
            { x: 60, y: 70, role: "Defensa" },
            { x: 80, y: 60, role: "Defensa" },
            // Centrocampistas (4)
            { x: 20, y: 38, role: "Centrocampista" },
            { x: 40, y: 48, role: "Centrocampista" },
            { x: 60, y: 48, role: "Centrocampista" },
            { x: 80, y: 38, role: "Centrocampista" },
            // Delanteros (2)
            { x: 40, y: 25, role: "Delantero" },
            { x: 60, y: 25, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Zona Muerta": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },

            // Defensas (5)
            { x: 15, y: 65, role: "Defensa" },
            { x: 35, y: 75, role: "Defensa" },
            { x: 50, y: 65, role: "Defensa" },
            { x: 65, y: 75, role: "Defensa" },
            { x: 85, y: 65, role: "Defensa" },

            // Centrocampistas (3)
            { x: 22, y: 40, role: "Centrocampista" },
            { x: 50, y: 45, role: "Centrocampista" },
            { x: 77, y: 40, role: "Centrocampista" },

            // Delanteros (2)
            { x: 40, y: 20, role: "Delantero" },
            { x: 60, y: 20, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    Reja: {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (4)
            { x: 20, y: 70, role: "Defensa" },
            { x: 40, y: 70, role: "Defensa" },
            { x: 60, y: 70, role: "Defensa" },
            { x: 80, y: 70, role: "Defensa" },
            // Centrocampistas (4)
            { x: 20, y: 48, role: "Centrocampista" },
            { x: 40, y: 48, role: "Centrocampista" },
            { x: 60, y: 48, role: "Centrocampista" },
            { x: 80, y: 48, role: "Centrocampista" },
            // Delanteros (2)
            { x: 40, y: 25, role: "Delantero" },
            { x: 60, y: 25, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Árbol de Navidad": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },

            // Defensas (4)
            { x: 15, y: 70, role: "Defensa" },
            { x: 38, y: 70, role: "Defensa" },
            { x: 62, y: 70, role: "Defensa" },
            { x: 85, y: 70, role: "Defensa" },

            // Centrocampistas (5)
            { x: 25, y: 45, role: "Centrocampista" },
            { x: 50, y: 45, role: "Centrocampista" },
            { x: 75, y: 45, role: "Centrocampista" },
            { x: 37, y: 30, role: "Centrocampista" },
            { x: 63, y: 30, role: "Centrocampista" },

            // Delanteros (1)
            { x: 50, y: 15, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Ataque Trillizo": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (4)
            { x: 20, y: 70, role: "Defensa" },
            { x: 40, y: 70, role: "Defensa" },
            { x: 60, y: 70, role: "Defensa" },
            { x: 80, y: 70, role: "Defensa" },
            // Centrocampistas (3)
            { x: 30, y: 48, role: "Centrocampista" },
            { x: 50, y: 48, role: "Centrocampista" },
            { x: 70, y: 48, role: "Centrocampista" },
            // Delanteros (3)
            { x: 20, y: 25, role: "Delantero" },
            { x: 50, y: 25, role: "Delantero" },
            { x: 80, y: 25, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Flecha Espectral": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },

            // Defensas (4)
            { x: 15, y: 70, role: "Defensa" },
            { x: 38, y: 70, role: "Defensa" },
            { x: 62, y: 70, role: "Defensa" },
            { x: 85, y: 70, role: "Defensa" },

            // Centrocampistas (3)
            { x: 25, y: 45, role: "Centrocampista" },
            { x: 50, y: 45, role: "Centrocampista" },
            { x: 75, y: 45, role: "Centrocampista" },
            { x: 37, y: 25, role: "Centrocampista" },
            { x: 63, y: 25, role: "Centrocampista" },

            // Delanteros (3)
            { x: 50, y: 10, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    Pirámide: {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },

            // Defensas (4)
            { x: 38, y: 70, role: "Defensa" },
            { x: 62, y: 70, role: "Defensa" },

            // Centrocampistas (3)
            { x: 25, y: 45, role: "Centrocampista" },
            { x: 50, y: 45, role: "Centrocampista" },
            { x: 75, y: 45, role: "Centrocampista" },

            // Delanteros (3)
            { x: 10, y: 20, role: "Delantero" },
            { x: 30, y: 20, role: "Delantero" },
            { x: 50, y: 20, role: "Delantero" },
            { x: 70, y: 20, role: "Delantero" },
            { x: 90, y: 20, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Alas de Grulla": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (4)
            { x: 30, y: 66, role: "Defensa" },
            { x: 50, y: 60, role: "Defensa" },
            { x: 70, y: 66, role: "Defensa" },
            { x: 50, y: 75, role: "Defensa" },
            // Centrocampistas (4)
            { x: 15, y: 35, role: "Centrocampista" },
            { x: 20, y: 50, role: "Centrocampista" },
            { x: 80, y: 50, role: "Centrocampista" },
            { x: 85, y: 35, role: "Centrocampista" },
            // Delanteros (2)
            { x: 12, y: 20, role: "Delantero" },
            { x: 88, y: 20, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    Jungla: {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (3)
            { x: 30, y: 70, role: "Defensa" },
            { x: 50, y: 70, role: "Defensa" },
            { x: 70, y: 70, role: "Defensa" },
            // Centrocampistas (4)
            { x: 20, y: 48, role: "Centrocampista" },
            { x: 40, y: 48, role: "Centrocampista" },
            { x: 60, y: 48, role: "Centrocampista" },
            { x: 80, y: 48, role: "Centrocampista" },
            // Delanteros (2)
            { x: 30, y: 25, role: "Delantero" },
            { x: 50, y: 25, role: "Delantero" },
            { x: 70, y: 25, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
    "Puerta al Cielo": {
        positions: [
            { x: 50, y: 90.5, role: "Portero" },
            // Defensas (4)
            { x: 20, y: 60, role: "Defensa" },
            { x: 40, y: 70, role: "Defensa" },
            { x: 60, y: 70, role: "Defensa" },
            { x: 80, y: 60, role: "Defensa" },
            // Centrocampistas (4)
            { x: 20, y: 38, role: "Centrocampista" },
            { x: 40, y: 48, role: "Centrocampista" },
            { x: 60, y: 48, role: "Centrocampista" },
            { x: 80, y: 38, role: "Centrocampista" },
            // Delanteros (2)
            { x: 50, y: 15, role: "Delantero" },
            { x: 50, y: 30, role: "Delantero" },
        ],
        ignoreLayout: true,
    },
};
