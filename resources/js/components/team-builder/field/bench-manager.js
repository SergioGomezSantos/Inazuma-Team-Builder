// field/bench-manager.js
import { NameTagManager } from "./name-tag-manager.js";
import FieldRenderer from "./field-renderer.js";

export class BenchManager {
    static createBenchPositions() {
        const benchContainer = document.querySelector(
            ".bg-gray-100.dark\\:bg-gray-700"
        );
        if (!benchContainer) return;

        const benchPositions = [
            { id: "bench-0", x: 25, y: 25, role: "Substitute", isBench: true },
            { id: "bench-1", x: 75, y: 25, role: "Substitute", isBench: true },
            { id: "bench-2", x: 15, y: 75, role: "Substitute", isBench: true },
            { id: "bench-3", x: 50, y: 75, role: "Substitute", isBench: true },
            { id: "bench-4", x: 85, y: 75, role: "Substitute", isBench: true },
        ];

        benchPositions.forEach((pos) => {
            let benchEl = document.querySelector(
                `[data-position-id="${pos.id}"]`
            );

            if (benchEl) {
                this.initializeBenchPosition(benchEl, pos);
            } else {
                benchEl = this.createBenchElement(pos);
                this.appendToBenchContainer(benchEl, pos.id, benchContainer);
                this.initializeBenchPosition(benchEl, pos);
            }
        });
    }

    static initializeBenchPosition(benchEl, pos) {
        if (!benchEl.querySelector("img")) {
            benchEl.innerHTML = "";

            // Añadir placeholder
            const placeholder = document.createElement("img");
            placeholder.src = "/storage/players/placeholder.png";
            placeholder.className = "w-full h-full object-cover opacity-50";
            placeholder.setAttribute("data-is-placeholder", "true");
            placeholder.setAttribute("data-player-name", "undefined"); // Cambiado a undefined
            benchEl.appendChild(placeholder);

            // Aplicar estilos iniciales correctos
            benchEl.className =
                "bench-position w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20 bg-none border-none";
            benchEl.style.cssText = "transform: scale(1); z-index: 10;";

            // Crear name tag vacío
            NameTagManager.createEmptyNameTag(benchEl, {
                showNames: FieldRenderer.showNamesGlobal,
                isBench: true,
            });
        }
    }

    static createBenchElement(pos) {
        const benchEl = document.createElement("div");
        benchEl.className =
            "bench-position w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20";
        benchEl.dataset.positionId = pos.id;
        benchEl.dataset.positionRole = pos.role;
        benchEl.draggable = true;
        return benchEl;
    }

    static appendToBenchContainer(benchEl, id, container) {
        const row = ["bench-0", "bench-1"].includes(id)
            ? container.querySelector(".flex > .flex:first-child")
            : container.querySelector(".flex > .flex:last-child");
        row?.appendChild(benchEl);
    }
}
