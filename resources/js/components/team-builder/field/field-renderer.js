import { PositionManager } from "./position-manager.js";
import { BenchManager } from "./bench-manager.js";
import { NameTagManager } from "./name-tag-manager.js";
import TeamManager from "../team/team-manager.js";

export default class FieldRenderer {
    static showNamesGlobal = true;
    static borderedDesign = false;

    static init() {
        this.initBenchPositionsFromDOM();
    }

    static initBenchPositionsFromDOM() {
        document.querySelectorAll(".bench-position").forEach((el, index) => {
            const role = el.dataset.positionRole || `bench-${index}`;
            const pos = {
                id: el.dataset.positionId,
                role,
                isBench: true,
                x: 0,
                y: 0,
            };

            this.setupPositionContent(el, pos);
            this.setupPositionEvents(el);
        });
    }

    static toggleShowNames() {
        this.showNamesGlobal = !this.showNamesGlobal;
        NameTagManager.updateAllNameTags(this.showNamesGlobal);
        return this.showNamesGlobal;
    }

    static toggleDesign() {
        this.borderedDesign = !this.borderedDesign;
        this.updateAllPositions();
        this.updateAllPlayerImages();
        return this.borderedDesign;
    }

    static updateAllPositions() {
        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((position) => {
                this.resetPositionStyle(position);
            });
    }

    static updateAllPlayerImages() {
        document
            .querySelectorAll(".field-position img, .bench-position img")
            .forEach((img) => {
                img.className = `w-full h-full object-cover ${
                    this.borderedDesign
                        ? "rounded-full"
                        : "rounded-t-full rounded-b-none"
                }`;
            });
    }

    static renderPositions(positions) {
        this.clearFieldPositions();
        positions.forEach((pos) => this.createPositionElement(pos));
        BenchManager.createBenchPositions();
    }

    static clearFieldPositions() {
        document
            .querySelectorAll(".field-position:not(.static-field-mark)")
            .forEach((pos) => pos.remove());
    }

    static createPositionElement(pos) {
        const positionEl = PositionManager.createPositionElement(pos, {
            showNamesGlobal: this.showNamesGlobal,
            borderedDesign: this.borderedDesign,
        });

        this.setupPositionContent(positionEl, pos);
        this.setupPositionEvents(positionEl);

        document.querySelector(".soccer-field").appendChild(positionEl);
        return positionEl;
    }

    static setupPositionContent(positionEl, pos) {
        positionEl.innerHTML = ""; // Limpiar siempre el contenido

        // Añadir placeholder solo si no hay jugador
        if (!positionEl.querySelector("img[data-player-id]")) {
            const placeholder = document.createElement("img");
            placeholder.src = "/storage/players/placeholder.png";
            placeholder.className = "w-full h-full object-cover opacity-50";
            placeholder.setAttribute("data-is-placeholder", "true");
            placeholder.setAttribute("data-player-name", pos.role);
            positionEl.appendChild(placeholder);
        }

        // Siempre crear name tag
        NameTagManager.createNameTag(positionEl, {
            showNames: this.showNamesGlobal,
            isBench: pos.isBench,
            playerName: positionEl
                .querySelector("img[data-player-id]")
                ?.getAttribute("data-player-name"),
            positionRole: pos.role,
        });
    }

    static setupPositionEvents(positionEl) {
        if (positionEl.parentNode) {
            positionEl = this.cleanPositionEvents(positionEl);
        }

        PositionManager.setupPositionEvents(positionEl, {
            handleDragStart: (e) => this.handlePositionDragStart(e, positionEl),
            handleDragOver: (e) => this.handlePositionDragOver(e, positionEl),
            handleDragLeave: () => this.resetPositionStyle(positionEl),
            handleDrop: (e) => this.handlePositionDrop(e, positionEl),
        });

        this.setupHoverEffects(positionEl);
    }

    static cleanPositionEvents(positionEl) {
        const newElement = positionEl.cloneNode(true);

        if (positionEl.parentNode) {
            positionEl.parentNode.replaceChild(newElement, positionEl);
        }

        return newElement;
    }

    static handlePositionDragStart(e, positionEl) {
        const img = positionEl.querySelector("img[data-player-id]");
        if (img) {
            e.dataTransfer.setData(
                "text/plain",
                img.getAttribute("data-player-id")
            );
            e.dataTransfer.setData(
                "from-position-id",
                positionEl.dataset.positionId
            );
            e.dataTransfer.setData(
                "from-position-role",
                positionEl.dataset.positionRole
            );
            e.dataTransfer.effectAllowed = "move";
            e.dataTransfer.setDragImage(img, 50, 50);
        } else {
            e.preventDefault();
        }
    }

    static handlePositionDragOver(e, positionEl) {
        e.preventDefault();
        positionEl.style.transform = positionEl.classList.contains(
            "bench-position"
        )
            ? "scale(1.1)"
            : "translate(-50%, -50%) scale(1.1)";
        positionEl.style.zIndex = "20";
        if (this.borderedDesign) {
            positionEl.style.border = "2px solid #f59e0b";
        }
    }

    static handlePositionDrop(e, positionEl) {
        e.preventDefault();
        this.resetPositionStyle(positionEl);

        const playerId = e.dataTransfer.getData("text/plain");
        const fromPositionId = e.dataTransfer.getData("from-position-id");

        if (!fromPositionId) {
            const player =
                document.querySelector(`[data-player-id="${playerId}"]`) ||
                document.getElementById(playerId);
            if (player && window.TeamManager) {
                TeamManager.addPlayerToPosition(positionEl, player);
            }
        } else {
            this.swapPlayers(positionEl, fromPositionId);
        }
    }

    static swapPlayers(positionEl, fromPositionId) {
        const sourcePositionEl = document.querySelector(
            `[data-position-id="${fromPositionId}"]`
        );
        if (sourcePositionEl && sourcePositionEl !== positionEl) {
            const tempContent = positionEl.innerHTML;
            positionEl.innerHTML = sourcePositionEl.innerHTML;
            sourcePositionEl.innerHTML = tempContent;

            if (!sourcePositionEl.querySelector("img[data-player-id]")) {
                this.resetPositionStyle(sourcePositionEl);
            }

            this.updatePositionEvents(positionEl);
            this.updatePositionEvents(sourcePositionEl);
        }
    }

    static updatePositionEvents(positionEl) {
        const parent = positionEl.parentNode;
        if (!parent || !parent.contains(positionEl)) {
            return positionEl;
        }

        const newElement = positionEl.cloneNode(true);
        parent.replaceChild(newElement, positionEl);
        this.setupPositionEvents(newElement);
        return newElement;
    }

    static setupHoverEffects(positionEl) {
        this.removeHoverEffects(positionEl);

        const removeBtn = positionEl.querySelector("button");
        const playerImg = positionEl.querySelector("img[data-player-id]");
        if (!removeBtn || !playerImg) return;

        const nameTag =
            positionEl.querySelector(".player-name-tag") ||
            NameTagManager.createNameTag(positionEl, {
                showNames: this.showNamesGlobal,
                isBench: positionEl.classList.contains("bench-position"),
                playerName: playerImg.getAttribute("data-player-name"),
                positionRole: positionEl.dataset.positionRole,
            });

        const mouseEnterHandler = () => {
            removeBtn.classList.replace("opacity-0", "opacity-100");
            positionEl.style.transform = positionEl.classList.contains(
                "bench-position"
            )
                ? "scale(1.1)"
                : "translate(-50%, -50%) scale(1.1)";
            positionEl.style.zIndex = "20";

            if (!this.showNamesGlobal) {
                nameTag.style.display = "block";
            }
        };

        const mouseLeaveHandler = () => {
            this.resetPositionStyle(positionEl);
            if (!this.showNamesGlobal) {
                nameTag.style.display = "none";
            }
        };

        const removeBtnClickHandler = (e) => {
            e.stopPropagation();
            positionEl.innerHTML = "";
            this.resetPositionStyle(positionEl);
            this.removeHoverEffects(positionEl);
        };

        positionEl.addEventListener("mouseenter", mouseEnterHandler);
        positionEl.addEventListener("mouseleave", mouseLeaveHandler);
        removeBtn.addEventListener("click", removeBtnClickHandler);

        // Guardar referencias para posible limpieza
        positionEl._mouseEnterHandler = mouseEnterHandler;
        positionEl._mouseLeaveHandler = mouseLeaveHandler;
        positionEl._removeBtnClickHandler = removeBtnClickHandler;
    }

    static removeHoverEffects(positionEl) {
        if (positionEl._mouseEnterHandler) {
            positionEl.removeEventListener(
                "mouseenter",
                positionEl._mouseEnterHandler
            );
            delete positionEl._mouseEnterHandler;
        }
        if (positionEl._mouseLeaveHandler) {
            positionEl.removeEventListener(
                "mouseleave",
                positionEl._mouseLeaveHandler
            );
            delete positionEl._mouseLeaveHandler;
        }
        const removeBtn = positionEl.querySelector("button");
        if (removeBtn && positionEl._removeBtnClickHandler) {
            removeBtn.removeEventListener(
                "click",
                positionEl._removeBtnClickHandler
            );
            delete positionEl._removeBtnClickHandler;
        }
    }

    static resetPositionStyle(positionEl) {
        positionEl.style.transform = positionEl.classList.contains(
            "bench-position"
        )
            ? "scale(1)"
            : "translate(-50%, -50%)";
        positionEl.style.zIndex = "10";

        // Aplicar clases de Tailwind directamente
        if (this.borderedDesign) {
            positionEl.style.border = "none";
            positionEl.classList.add(
                "bg-white",
                "dark:bg-gray-800",
                "shadow-sm",
                "border-2",
                "border-gray-200",
                "dark:border-gray-600"
            );
            positionEl.classList.remove("bg-none", "border-none");
        } else {
            positionEl.classList.add("bg-none", "border-none");
            positionEl.classList.remove(
                "bg-white",
                "dark:bg-gray-800",
                "shadow-sm",
                "border-2",
                "border-gray-200",
                "dark:border-gray-600"
            );
        }

        const removeBtn = positionEl.querySelector("button");
        if (removeBtn) {
            removeBtn.classList.replace("opacity-100", "opacity-0");
        }

        if (!positionEl.querySelector("img[data-player-id]")) {
            this.setupPlaceholder(positionEl);
        } else {
            // Actualizar name tag si hay jugador
            const playerImg = positionEl.querySelector("img[data-player-id]");
            NameTagManager.updateNameTag(positionEl, {
                showNames: this.showNamesGlobal,
                playerName: playerImg.getAttribute("data-player-name"),
                positionRole: positionEl.dataset.positionRole,
            });
        }
    }

    static setupPlaceholder(positionEl) {
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
            showNames: this.showNamesGlobal,
            isBench: positionEl.classList.contains("bench-position"),
            playerName: null,
            positionRole: positionEl.dataset.positionRole,
        });
    }

    static assignPlayerToPosition(
        positionEl,
        { playerId, playerName, playerImageSrc }
    ) {
        positionEl.innerHTML = ""; // limpiar

        const img = document.createElement("img");
        img.src = playerImageSrc || "/storage/players/placeholder.png";
        img.setAttribute("data-player-id", playerId);
        img.setAttribute("data-player-name", playerName);
        img.className = this.borderedDesign
            ? "w-full h-full object-cover rounded-full"
            : "w-full h-full object-cover rounded-t-full rounded-b-none";

        positionEl.appendChild(img);

        const removeBtn = document.createElement("button");
        removeBtn.innerHTML = "&times;";
        removeBtn.className =
            "absolute top-0 left-0 text-xs text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 transition-opacity duration-200 z-20";
        positionEl.appendChild(removeBtn);

        // Actualizar el name tag con el nombre del jugador
        NameTagManager.updateNameTag(positionEl, {
            showNames: this.showNamesGlobal,
            playerName,
            positionRole: positionEl.dataset.positionRole,
        });

        // Reaplicar eventos y estilos
        this.resetPositionStyle(positionEl);
        this.setupPositionEvents(positionEl);
    }
}
