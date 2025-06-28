export class TeamActions {
    static clearTeam() {
        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((pos) => {
                TeamManager.clearPosition(pos);
            });
    }

    static generateRandomTeam() {
        this.clearTeam();

        // Field
        document.querySelectorAll(".field-position").forEach((position) => {
            const positionRole = position.dataset.positionRole;
            const matchingPlayers = Array.from(
                document.querySelectorAll(".list-player")
            ).filter(
                (p) =>
                    p.dataset.position.toLowerCase() ===
                    positionRole.toLowerCase()
            );

            if (matchingPlayers.length > 0) {
                const randomPlayer =
                    matchingPlayers[
                        Math.floor(Math.random() * matchingPlayers.length)
                    ];
                TeamManager.addPlayerToPosition(position, randomPlayer);
            }
        });

        // Bench
        document.querySelectorAll(".bench-position").forEach((position) => {
            const allPlayers = Array.from(
                document.querySelectorAll(".list-player")
            );
            if (allPlayers.length > 0) {
                const randomPlayer =
                    allPlayers[Math.floor(Math.random() * allPlayers.length)];
                TeamManager.addPlayerToPosition(position, randomPlayer);
            }
        });

        if (window.ImageSelector) {
            window.ImageSelector.randomizeSelections();
        }
    }
}
