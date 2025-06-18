export default class TeamManager {
    static init() {
        this.clearTeamBtn = document.getElementById("clear-team");
        this.randomTeamBtn = document.getElementById("random-team");
        this.setupEventListeners();
    }

    static setupEventListeners() {
        this.clearTeamBtn.addEventListener("click", () => this.clearTeam());
        this.randomTeamBtn.addEventListener("click", () =>
            this.generateRandomTeam()
        );
    }

    static addPlayerToPosition(positionElement, playerElement) {
        console.log("[ADD PLAYER] Datos del jugador:", {
            id: playerElement.id,
            dataset: playerElement.dataset,
            tagName: playerElement.tagName,
        });

        positionElement.innerHTML = "";

        // Manejar tanto elementos img como wrappers
        const playerImg =
            playerElement.tagName === "IMG"
                ? playerElement
                : playerElement.querySelector("img");

        if (!playerImg) {
            console.error("No se encontró imagen de jugador");
            return;
        }

        // Crear nueva imagen (no clonar)
        const newImg = document.createElement("img");
        newImg.src = playerImg.dataset.src || playerImg.src;
        newImg.className =
            "w-full h-full rounded-full object-cover border-2 border-yellow-400";
        newImg.setAttribute("data-player-id", playerElement.dataset.playerId);
        newImg.draggable = true;

        // Configurar eventos de arrastre para la imagen
        newImg.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData(
                "text/plain",
                playerElement.dataset.playerId
            );
            e.dataTransfer.setData(
                "from-position-id",
                positionElement.dataset.positionId
            );
            e.dataTransfer.setData(
                "from-position-role",
                positionElement.dataset.positionRole
            );
            e.dataTransfer.effectAllowed = "move";
            e.dataTransfer.setDragImage(newImg, 50, 50);
        });

        const removeBtn = document.createElement("button");
        removeBtn.innerHTML = "&times;";
        removeBtn.className =
            "absolute top-0 left-0 text-s bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0 transition-opacity duration-200";
        removeBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            positionElement.innerHTML = "";
        });

        // Mostrar el botón al hacer hover
        positionElement.addEventListener("mouseenter", () => {
            removeBtn.classList.remove("opacity-0");
            removeBtn.classList.add("opacity-100");
        });

        // Ocultar el botón al salir del hover
        positionElement.addEventListener("mouseleave", () => {
            removeBtn.classList.remove("opacity-100");
            removeBtn.classList.add("opacity-0");
        });

        positionElement.appendChild(newImg);
        positionElement.appendChild(removeBtn);
    }

    static clearTeam() {
        document.querySelectorAll(".field-position").forEach((pos) => {
            pos.innerHTML = "";
        });
    }

    static generateRandomTeam() {
        this.clearTeam();

        document.querySelectorAll(".field-position").forEach((position) => {
            const positionRole = position.dataset.position;
            const visiblePlayers = Array.from(
                document.querySelectorAll(".list-player")
            ).filter(
                (p) =>
                    p.style.display !== "none" &&
                    p.dataset.position.toLowerCase() ===
                        positionRole.toLowerCase()
            );

            if (visiblePlayers.length === 0) return;

            const randomIndex = Math.floor(
                Math.random() * visiblePlayers.length
            );
            const randomPlayer = visiblePlayers[randomIndex];

            this.addPlayerToPosition(position, randomPlayer);
        });

        if (window.ImageSelector) {
            window.ImageSelector.randomizeSelections();
        }
    }
}
