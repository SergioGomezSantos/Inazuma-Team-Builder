import LazyLoader from "./images-interaction/lazy-loader.js";

export default class PlayerSearch {
    static init() {
        this.searchInput = document.getElementById("player-search");
        this.playerItems = document.querySelectorAll(".list-player");
        if (this.searchInput) {
            this.setupEventListeners();
            this.autoFillTeamName();
        }
    }

    static autoFillTeamName() {
        if (savedTeamId >= 1 && savedTeamId <= 54) {
            this.searchInput.value = savedTeamName;
            this.filterPlayers();
        }
    }

    static setupEventListeners() {
        let searchTimeout;

        this.searchInput.addEventListener("input", () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.filterPlayers(), 300);
        });
    }

    static filterPlayers() {
        const searchTerm = this.searchInput.value.toLowerCase().trim();
        let anyChange = false;

        // Acentos
        const normalizedSearchTerm = searchTerm
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");

        const positionMap = {
            pr: "portero",
            df: "defensa",
            md: "centrocampista",
            medio: "centrocampista",
            mediocentro: "centrocampista",
            dl: "delantero",
        };

        const translatedPosition = positionMap[searchTerm] || null;

        this.playerItems.forEach((player) => {
            // Normalizar y convertir a minúsculas los datos de cada jugador
            const playerName = player.dataset.name
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();
            const playerFullName = player.dataset.fullname
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();
            const playerTeam = player.dataset.team
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();
            const playerPosition = player.dataset.position
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();
            const playerElement = player.dataset.element
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();

            const shouldBeVisible =
                searchTerm === "" ||
                playerName.includes(normalizedSearchTerm) ||
                playerFullName.includes(normalizedSearchTerm) ||
                playerTeam.includes(normalizedSearchTerm) ||
                playerPosition.includes(normalizedSearchTerm) ||
                playerElement.includes(normalizedSearchTerm) ||
                (translatedPosition &&
                    playerPosition.includes(translatedPosition));

            if (player.style.display !== (shouldBeVisible ? "flex" : "none")) {
                player.style.display = shouldBeVisible ? "flex" : "none";
                anyChange = true;
            }
        });

        if (anyChange) {
            LazyLoader.refresh();
        }
    }

    static clearSearch() {
        if (this.searchInput) {
            this.searchInput.value = "";
            this.filterPlayers();
        }
    }
}
