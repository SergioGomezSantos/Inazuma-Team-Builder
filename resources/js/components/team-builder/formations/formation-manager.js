import { SPECIAL_FORMATIONS } from "./formations-data/special-formations.js";
import { getSpecialFormation, findFormationByName } from "./formation-utils.js";
import FieldRenderer from "../field/field-renderer.js";

export default class FormationManager {
    static currentFormation = null;
    static SPECIAL_FORMATIONS = SPECIAL_FORMATIONS;

    static init() {
        this.selectElement = document.getElementById("formation-select");
        if (!this.selectElement) return;

        this.setupEventListeners();
        this.loadInitialFormation();
    }

    static setupEventListeners() {
        this.selectElement.addEventListener("change", () => {
            this.updateFormation(this.selectElement.value);
        });
    }

    static loadInitialFormation() {
        this.updateFormation(this.selectElement.value);
        this.assignPlayersFromTeam();
    }

    static updateFormation(formationId) {
        // Save player - pos
        const savedPositions = {};
        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((posEl) => {
                const playerImg = posEl.querySelector("img[data-player-id]");
                if (playerImg) {
                    savedPositions[posEl.dataset.positionId] = {
                        playerId: playerImg.getAttribute("data-player-id"),
                        playerName: playerImg.getAttribute("data-player-name"),
                        playerImageSrc: playerImg.src,
                    };
                }
            });

        // Select pos for Formation
        const selectedOption =
            this.selectElement.options[this.selectElement.selectedIndex];
        const specialFormation = findFormationByName(
            selectedOption.textContent.trim(),
            this.SPECIAL_FORMATIONS
        );

        if (specialFormation) {
            this.currentFormation = getSpecialFormation(
                specialFormation,
                this.SPECIAL_FORMATIONS
            );
            // New formation
            FieldRenderer.renderPositions(this.currentFormation);

            // Restore player - pos
            this.restorePlayersToPositions(savedPositions);
        } else {
            console.warn(
                "Formación no reconocida:",
                selectedOption.textContent.trim()
            );
        }
    }

    static restorePlayersToPositions(savedPositions) {
        Object.entries(savedPositions).forEach(([positionId, playerData]) => {
            const positionEl = document.querySelector(
                `[data-position-id="${positionId}"]`
            );
            if (positionEl) {
                const currentPlayerImg = positionEl.querySelector(
                    "img[data-player-id]"
                );
                if (
                    !currentPlayerImg ||
                    currentPlayerImg.getAttribute("data-player-id") !==
                        playerData.playerId
                ) {
                    FieldRenderer.assignPlayerToPosition(positionEl, {
                        playerId: playerData.playerId,
                        playerName: playerData.playerName,
                        playerImageSrc: playerData.playerImageSrc,
                    });
                }
            }
        });
    }

    static assignPlayersFromTeam() {
        if (!savedTeamPlayers) return;

        // Set player - pos on load team
        savedTeamPlayers.forEach((player) => {
            const positionEl = document.querySelector(
                `[data-position-id="${player.pivot.position_id}"]`
            );

            if (positionEl) {
                FieldRenderer.assignPlayerToPosition(positionEl, {
                    playerId: player.id,
                    playerName: player.name,
                    playerImageSrc: player.image
                        ? "/storage/players/" + player.image
                        : "/storage/players/placeholder.png",
                });
            }
        });
    }
}
