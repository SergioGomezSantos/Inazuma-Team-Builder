export class PositionManager {
    static createPositionElement(pos, { showNamesGlobal, borderedDesign }) {
        const positionEl = document.createElement("div");

        // Clases base para todas las posiciones
        let baseClasses =
            "field-position absolute w-24 h-24 rounded-full flex items-center justify-center cursor-pointer z-10 transition-all duration-150 ease-out";

        if (borderedDesign) {
            baseClasses +=
                " bg-white dark:bg-gray-800 shadow-sm border-2 border-gray-200 dark:border-gray-600";
        } else {
            baseClasses += " bg-none border-none";
        }

        positionEl.className = baseClasses;

        positionEl.style.cssText = `
            left: ${pos.x}%;
            top: ${pos.y}%;
            transform: translate(-50%, -50%);
            transform-origin: center center;
        `;

        positionEl.dataset.positionId = pos.id;
        positionEl.dataset.positionRole = pos.role;

        return positionEl;
    }

    static setupPositionEvents(
        positionEl,
        { handleDragStart, handleDragOver, handleDragLeave, handleDrop }
    ) {
        positionEl.addEventListener("dragstart", handleDragStart);
        positionEl.addEventListener("dragover", handleDragOver);
        positionEl.addEventListener("dragleave", handleDragLeave);
        positionEl.addEventListener("drop", handleDrop);

        return positionEl;
    }
}
