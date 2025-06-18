export default class PlayerSearch {
    static init() {
        this.searchInput = document.getElementById("player-search");
        this.playerItems = document.querySelectorAll(".list-player");
        if (this.searchInput) {
            this.setupEventListeners();
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

        this.playerItems.forEach((player) => {
            const shouldBeVisible =
                searchTerm === "" ||
                player.dataset.name.includes(searchTerm) ||
                player.dataset.fullname.includes(searchTerm) ||
                player.dataset.team.includes(searchTerm);

            if (player.style.display !== (shouldBeVisible ? "flex" : "none")) {
                player.style.display = shouldBeVisible ? "flex" : "none";
                anyChange = true;
            }
        });

        if (anyChange) {
            LazyLoader.refresh();
        }
    }
}
