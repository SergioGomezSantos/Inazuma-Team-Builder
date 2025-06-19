import FieldRenderer from "../field/field-renderer.js";
import { NameTagManager } from "../field/name-tag-manager.js";
import PlayerSearch from "../player-search.js";

export default class TeamManager {
    static init() {
        this.clearTeamBtn = document.getElementById("clear-team");
        this.randomTeamBtn = document.getElementById("random-team");
        this.toggleNamesBtn = document.getElementById("toggle-names");
        this.toggleDesignBtn = document.getElementById("toggle-design");
        this.saveTeamBtn = document.getElementById("save-team");

        this.setupEventListeners();
    }

    static setupEventListeners() {
        this.clearTeamBtn.addEventListener("click", () => this.clearTeam());
        this.randomTeamBtn.addEventListener("click", () =>
            this.generateRandomTeam()
        );

        this.toggleNamesBtn?.addEventListener(
            "click",
            this.handleToggleNames.bind(this)
        );
        this.toggleDesignBtn?.addEventListener(
            "click",
            this.handleToggleDesign.bind(this)
        );

        this.saveTeamBtn.addEventListener("click", () => this.saveTeam());
    }

    static handleToggleNames() {
        const isShowing = FieldRenderer.toggleShowNames();
        this.toggleIcon("show-names-icon", "hide-names-icon", isShowing);
    }

    static handleToggleDesign() {
        const isBordered = FieldRenderer.toggleDesign();
        this.toggleIcon("show-design-icon", "hide-design-icon", isBordered);
    }

    static toggleIcon(showId, hideId, isShowing) {
        const showIcon = document.getElementById(showId);
        const hideIcon = document.getElementById(hideId);

        if (showIcon && hideIcon) {
            showIcon.classList.toggle("hidden", !isShowing);
            hideIcon.classList.toggle("hidden", isShowing);
        }
    }

    static addPlayerToPosition(positionElement, playerElement) {
        // Limpiar contenido existente
        positionElement.querySelector("img[data-player-id]")?.remove();
        positionElement.querySelector("button")?.remove();
        positionElement
            .querySelector('img[data-is-placeholder="true"]')
            ?.remove();

        // Crear nueva imagen de jugador
        const playerImg =
            playerElement.tagName === "IMG"
                ? playerElement
                : playerElement.querySelector("img");

        const newImg = document.createElement("img");
        newImg.src = playerImg.dataset.src || playerImg.src;
        newImg.className = `w-full h-full object-cover ${
            FieldRenderer.borderedDesign
                ? "rounded-full"
                : "rounded-t-full rounded-b-none"
        }`;
        newImg.setAttribute("data-player-id", playerElement.dataset.playerId);
        newImg.setAttribute(
            "data-player-name",
            playerElement.dataset.name ||
                playerElement.dataset.playerName ||
                positionElement.dataset.positionRole
        );
        newImg.draggable = true;

        // Configurar eventos de arrastre
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

        // Crear botón de eliminar
        const removeBtn = document.createElement("button");
        removeBtn.innerHTML = "&times;";
        removeBtn.className =
            "absolute top-0 left-0 text-xs bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 transition-opacity duration-200 z-20";

        positionElement.appendChild(newImg);
        positionElement.appendChild(removeBtn);

        // Actualizar name tag inmediatamente
        NameTagManager.updateNameTag(positionElement, {
            showNames: FieldRenderer.showNamesGlobal,
            playerName: newImg.getAttribute("data-player-name"),
            positionRole: positionElement.dataset.positionRole,
        });

        FieldRenderer.setupHoverEffects(positionElement);

        // Actualizar etiqueta de nombre
        FieldRenderer.setupHoverEffects(positionElement);
    }

    static clearTeam() {
        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((pos) => {
                this.clearPosition(pos);
            });

        PlayerSearch.clearSearch();
    }

    static clearPosition(positionEl) {
        FieldRenderer.removeHoverEffects(positionEl);

        positionEl.querySelector("img[data-player-id]")?.remove();
        positionEl.querySelector("button")?.remove();

        // Restablecer placeholder y name tag
        positionEl.innerHTML = "";

        const placeholder = document.createElement("img");
        placeholder.src = "/storage/players/placeholder.png";
        placeholder.className = "w-full h-full object-cover opacity-50";
        placeholder.setAttribute("data-is-placeholder", "true");
        placeholder.setAttribute(
            "data-player-name",
            positionEl.dataset.positionRole
        );
        positionEl.appendChild(placeholder);

        NameTagManager.createNameTag(positionEl, {
            showNames: FieldRenderer.showNamesGlobal,
            isBench: positionEl.classList.contains("bench-position"),
            playerName: null,
            positionRole: positionEl.dataset.positionRole,
        });

        FieldRenderer.resetPositionStyle(positionEl);
    }

    static generateRandomTeam() {
        this.clearTeam();

        // Llenar posiciones de campo con jugadores del rol correspondiente
        document.querySelectorAll(".field-position").forEach((position) => {
            const positionRole = position.dataset.positionRole;
            const matchingPlayers = Array.from(
                document.querySelectorAll(".list-player")
            ).filter(
                (p) =>
                    p.dataset.position?.toLowerCase() ===
                    positionRole.toLowerCase()
            );

            if (matchingPlayers.length > 0) {
                const randomPlayer =
                    matchingPlayers[
                        Math.floor(Math.random() * matchingPlayers.length)
                    ];
                this.addPlayerToPosition(position, randomPlayer);
            }
        });

        // Llenar banquillo con cualquier jugador
        document.querySelectorAll(".bench-position").forEach((position) => {
            const allPlayers = Array.from(
                document.querySelectorAll(".list-player")
            );
            if (allPlayers.length > 0) {
                const randomPlayer =
                    allPlayers[Math.floor(Math.random() * allPlayers.length)];
                this.addPlayerToPosition(position, randomPlayer);
            }
        });

        if (window.ImageSelector) {
            window.ImageSelector.randomizeSelections();
        }
    }

    static getTeamPositions() {
        const positions = [];

        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((posEl) => {
                const positionId = posEl.dataset.positionId;
                const playerImg = posEl.querySelector("img[data-player-id]");
                const playerId = playerImg ? playerImg.dataset.playerId : null;

                positions.push({ positionId, playerId });
            });

        return positions;
    }

    static saveTeam() {
        const name = document.getElementById("team-name")?.value.trim();
        const emblem = document.getElementById("emblem-select")?.value.trim();
        const coach = document.getElementById("coach-select")?.value.trim();
        const formation = document
            .getElementById("formation-select")
            ?.value.trim();
        const positions = this.getTeamPositions();

        const payload = {
            name,
            emblem,
            coach,
            formation,
            positions,
        };

        fetch("/teams", {
            // Asegúrate que esta ruta coincide con tu route de Laravel
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((err) => Promise.reject(err));
                }
                return response.json();
            })
            .then((data) => {
                window.location.href = `/teams/${data.team.id}`;
            })
            .catch((error) => {
                let hasShownGenericAlert = false;
                if (error.errors) {
                    Object.entries(error.errors).forEach(
                        ([field, messages]) => {
                            if (field === "name") {
                                this.showNameError();
                            } else if (!hasShownGenericAlert) {
                                hasShownGenericAlert = true;
                                console.error("Error al guardar el equipo");
                            }
                        }
                    );
                } else if (!hasShownGenericAlert) {
                    console.error("Error al guardar el equipo");
                }
            });
    }

    static showNameError() {
        const nameInput = document.getElementById("team-name");
        nameInput.style.borderColor = "#ef4444";
        nameInput.style.borderWidth = "4px";

        const saveInput = document.getElementById("save-team");
        saveInput.style.backgroundColor = "#ef4444";

        setTimeout(() => {
            nameInput.style.borderColor = "";
            nameInput.style.borderWidth = "";
            saveInput.style.backgroundColor = "";
        }, 5000);
    }
}
