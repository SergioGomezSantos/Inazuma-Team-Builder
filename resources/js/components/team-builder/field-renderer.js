export default class FieldRenderer {
    static renderPositions(positions) {
        this.clearPositions();
        positions.forEach((pos) => this.createPositionElement(pos));
    }

    static clearPositions() {
        document.querySelectorAll(".field-position").forEach((pos) => {
            if (!pos.classList.contains("static-field-mark")) {
                pos.remove();
            }
        });
    }

    static createPositionElement(pos) {
        const field = document.querySelector(".soccer-field");
        const positionEl = document.createElement("div");

        // Identificadores únicos
        positionEl.dataset.positionId = pos.id;
        positionEl.dataset.positionRole = pos.role;
        positionEl.dataset.position = pos.role;

        // Estilos base
        positionEl.style.cssText = `
            position: absolute;
            width: 96px;
            height: 96px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 2px solid #d1d5db;
            left: ${pos.x}%;
            top: ${pos.y}%;
            z-index: 10;
            transform: translate(-50%, -50%);
        `;

        // Clases y modo oscuro
        positionEl.classList.add("field-position");
        if (document.documentElement.classList.contains("dark")) {
            positionEl.style.background = "#1f2937";
            positionEl.style.borderColor = "#4b5563";
        }

        // Configurar eventos
        this.setupPositionEvents(positionEl);

        field.appendChild(positionEl);
        return positionEl;
    }

    static setupPositionEvents(positionEl) {
        // Drag start
        positionEl.addEventListener("dragstart", (e) => {
            const img = positionEl.querySelector("img");
            if (img) {
                console.log("[Drag START] Transferiendo datos:", {
                    playerId: img.getAttribute("data-player-id"),
                    positionId: positionEl.dataset.positionId,
                    positionRole: positionEl.dataset.positionRole,
                });

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
                console.warn("Intento de arrastrar posición vacía");
                e.preventDefault();
            }
        });

        // Drag over
        positionEl.addEventListener("dragover", (e) => {
            e.preventDefault();
            positionEl.style.transform = "translate(-50%, -50%) scale(1.1)";
            positionEl.style.borderColor = "#f59e0b";
            positionEl.style.zIndex = "20";
        });

        // Drag leave
        positionEl.addEventListener("dragleave", () => {
            positionEl.style.transform = "translate(-50%, -50%)";
            positionEl.style.borderColor =
                document.documentElement.classList.contains("dark")
                    ? "#4b5563"
                    : "#d1d5db";
            positionEl.style.zIndex = "10";
        });

        // Drop
        // Drop event handler actualizado
        positionEl.addEventListener("drop", (e) => {
            e.preventDefault();

            // Restaurar estilos
            positionEl.style.transform = "translate(-50%, -50%)";
            positionEl.style.borderColor =
                document.documentElement.classList.contains("dark")
                    ? "#4b5563"
                    : "#d1d5db";
            positionEl.style.zIndex = "10";

            // Obtener datos transferidos
            const playerId = e.dataTransfer.getData("text/plain");
            const fromPositionId = e.dataTransfer.getData("from-position-id");
            const fromPositionRole =
                e.dataTransfer.getData("from-position-role");

            console.group("[DROP] Evento recibido");
            console.log("Datos transferidos:", {
                playerId,
                fromPositionId,
                fromPositionRole,
            });

            // Caso 1: Viene de lista de jugadores
            if (!fromPositionId) {
                console.log("Caso 1: Viene de lista de jugadores");
                const player =
                    document.querySelector(`[data-player-id="${playerId}"]`) ||
                    document.getElementById(playerId);

                if (player && window.TeamManager) {
                    positionEl.innerHTML = "";
                    window.TeamManager.addPlayerToPosition(positionEl, player);
                }
            }
            // Caso 2: Viene de otra posición del campo
            else {
                const sourcePositionEl = document.querySelector(
                    `[data-position-id="${fromPositionId}"]`
                );
                if (sourcePositionEl && sourcePositionEl !== positionEl) {
                    console.log("Caso 2: Intercambio entre posiciones");

                    // Intercambiar contenidos completos
                    const tempContent = positionEl.innerHTML;
                    positionEl.innerHTML = sourcePositionEl.innerHTML;
                    sourcePositionEl.innerHTML = tempContent;

                    // Actualizar eventos
                    this.updatePositionEvents(positionEl);
                    this.updatePositionEvents(sourcePositionEl);
                }
            }
            console.groupEnd();
        });
    }

    static updatePositionEvents(positionElement) {
        // Clonar el elemento completo (contenido y todo)
        const newElement = positionElement.cloneNode(true);

        // Copiar atributos importantes
        newElement.style.cssText = positionElement.style.cssText;
        newElement.dataset.positionId = positionElement.dataset.positionId;
        newElement.dataset.positionRole = positionElement.dataset.positionRole;
        newElement.dataset.position = positionElement.dataset.position;

        // Reemplazar en el DOM
        positionElement.replaceWith(newElement);

        // Reconfigurar eventos
        this.setupPositionEvents(newElement);

        // Reconfigurar botón de eliminar si existe
        const removeBtn = newElement.querySelector("button");
        if (removeBtn) {
            removeBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                newElement.innerHTML = "";
            });

            // Reconfigurar eventos hover
            newElement.addEventListener("mouseenter", () => {
                removeBtn.classList.remove("opacity-0");
                removeBtn.classList.add("opacity-100");
            });

            newElement.addEventListener("mouseleave", () => {
                removeBtn.classList.remove("opacity-100");
                removeBtn.classList.add("opacity-0");
            });
        }

        return newElement;
    }
}
