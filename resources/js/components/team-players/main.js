const playersData = window.teamPlayersData;
const statLabels = window.teamStatLabels;
const firstPlayerId = window.teamFirstPlayerId;

export default {
    init() {
        // Techniques and Stats tabs with styles on active/not
        function setupTabs() {
            const tabs = document.querySelectorAll(".tab-button");
            const contents = document.querySelectorAll(".tab-content");

            contents.forEach((c) => c.classList.add("hidden"));
            document
                .getElementById("techniques-content")
                .classList.remove("hidden");
            document
                .querySelector('[data-tab="techniques"]')
                .classList.add("bg-yellow-500", "text-white");

            tabs.forEach((tab) => {
                tab.addEventListener("click", () => {
                    tabs.forEach((t) => {
                        t.classList.remove("bg-yellow-500", "text-white");
                        t.classList.add(
                            "bg-gray-200",
                            "dark:bg-gray-700",
                            "text-gray-800",
                            "dark:text-gray-200"
                        );
                    });

                    tab.classList.remove(
                        "bg-gray-200",
                        "dark:bg-gray-700",
                        "text-gray-800",
                        "dark:text-gray-200"
                    );
                    tab.classList.add("bg-yellow-500", "text-white");

                    contents.forEach((content) =>
                        content.classList.add("hidden")
                    );
                    const target = tab.getAttribute("data-tab");
                    document
                        .getElementById(`${target}-content`)
                        .classList.remove("hidden");
                });
            });
        }

        // Collapsables techniques with svg change
        function setupCollapsables() {
            document.querySelectorAll(".collapse-toggle").forEach((btn) => {
                const targetId = btn.getAttribute("data-target");
                const target = document.getElementById(targetId);
                const icon = btn.querySelector(".icon-toggle");

                btn.addEventListener("click", () => {
                    const isHidden = target.classList.toggle("hidden");

                    icon.innerHTML = isHidden
                        ? `<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />`
                        : `<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />`;
                });
            });
        }

        // Update all data by player
        function updatePlayerDetails(player) {
            // General Info
            document.getElementById("detail-player-name").textContent =
                player.full_name;
            document.getElementById("detail-player-image").src = player.image;
            document.getElementById("detail-player-original-team").textContent =
                player.original_team;
            document.getElementById(
                "detail-player-position-icon"
            ).src = `/storage/icons/positions/${player.position.toLowerCase()}.webp`;
            document.getElementById(
                "detail-player-element-icon"
            ).src = `/storage/icons/elements/${player.element.toLowerCase()}.webp`;

            // Stats
            ["ie1", "ie2", "ie3"].forEach((version) => {
                Object.keys(statLabels).forEach((stat) => {
                    const el = document.querySelector(
                        `.player-stat[data-version="${version}"][data-stat="${stat}"]`
                    );
                    if (el)
                        el.textContent = player.stats[version]?.[stat] || "--";
                });
            });

            // Techniques
            ["anime1", "anime2", "anime3", "ie1", "ie2", "ie3"].forEach(
                (source) => {
                    const container = document.getElementById(
                        `techniques-${source}`
                    );
                    if (!container) return;

                    // Div with span and checking colors
                    if (player.techniques[source]?.length > 0) {
                        container.innerHTML = player.techniques[source]
                            .map(
                                (tech) => `
                            <div class="flex flex-col gap-1 border-b border-gray-300 dark:border-gray-600 pb-2">
                                <div class="flex gap-2 items-center">
                                    ${
                                        tech.element
                                            ? `<img src="/storage/icons/elements/${tech.element}.webp" class="w-5 h-5" alt="${tech.element}">`
                                            : `<span class="w-5 h-5 inline-block"></span>`
                                    }
                                    <img src="/storage/icons/types/${
                                        tech.type
                                    }.webp" class="w-7 h-5" alt="${tech.type}">
                                    <span>${tech.name}</span>
                                </div>
                                ${
                                    tech.with.length > 0
                                        ? `
                                        <div class="flex flex-wrap gap-2 text-sm pl-1 ml-12">
                                        <span>|</span>
                                        ${tech.with
                                            .map(
                                                (name) => `
                                                <span class="${
                                                    Object.values(
                                                        playersData
                                                    ).some(
                                                        (p) => p.name === name
                                                    )
                                                        ? "text-green-500"
                                                        : "text-red-500"
                                                } flex items-center gap-1">
                                                    ${name}
                                                </span>
                                            `
                                            )
                                            .join("")}
                                    </div>
                                    `
                                        : ""
                                }
                            </div>
                        `
                            )
                            .join("");
                    } else {
                        container.innerHTML =
                            '<div class="text-center py-4 text-gray-500">Sin técnicas registradas</div>';
                    }
                }
            );
        }

        // Border on selected player
        function setupPlayerSelection() {
            const playerList = document.getElementById("player-list");

            playerList.addEventListener("click", (e) => {
                const playerEl = e.target.closest(".list-player");
                if (!playerEl) return;

                const playerId = playerEl.dataset.playerId;
                const player = playersData[playerId];
                if (!player) return;

                document.querySelectorAll(".list-player").forEach((el) => {
                    el.classList.remove(
                        "border-yellow-500",
                        "dark:border-yellow-400"
                    );
                });
                playerEl.classList.add(
                    "border-yellow-500",
                    "dark:border-yellow-400"
                );

                updatePlayerDetails(player);
            });
        }

        // Init
        setupTabs();
        setupCollapsables();
        setupPlayerSelection();

        // Init on load
        const firstPlayer = playersData[firstPlayerId];
        if (firstPlayer) {
            document
                .querySelector(
                    `.list-player[data-player-id="${firstPlayerId}"]`
                )
                ?.classList.add("border-yellow-500", "dark:border-yellow-400");

            updatePlayerDetails(firstPlayer);
        }
    },
};
