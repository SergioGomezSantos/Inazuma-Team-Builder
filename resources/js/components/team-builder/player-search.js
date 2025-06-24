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
        if (savedTeamId >= 1 && savedTeamId <= 28) {
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
            const shouldBeVisible =
                searchTerm === "" ||
                player.dataset.name.includes(searchTerm) ||
                player.dataset.fullname.includes(searchTerm) ||
                player.dataset.team.includes(searchTerm) ||
                player.dataset.position.includes(searchTerm) ||
                player.dataset.element.includes(searchTerm) ||
                (translatedPosition &&
                    player.dataset.position.includes(translatedPosition));

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
