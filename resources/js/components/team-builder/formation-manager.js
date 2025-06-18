import FieldRenderer from "./field-renderer.js";

export default class FormationManager {
    static currentFormation = null;

    static init() {
        this.selectElement = document.getElementById("formation-select");
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
    }

    static updateFormation(formationId) {
        const selectedOption =
            this.selectElement.options[this.selectElement.selectedIndex];
        const layout = selectedOption.textContent.split(" - ")[0];
        this.currentFormation = this.calculatePositions(layout);
        FieldRenderer.renderPositions(this.currentFormation);
    }

    static calculatePositions(layout) {
        const parts = layout.split("-").map(Number);
        const positions = [];
        let positionCounter = 0;

        positions.push({
            x: 50,
            y: 93,
            role: "Portero",
            id: `pos-${positionCounter++}`,
        });

        const verticalSpacing = 70 / (parts.length + 1);

        parts.forEach((count, lineIndex) => {
            const yPos = 15 + verticalSpacing * (parts.length - lineIndex);
            const horizontalSpacing = 100 / (count + 1);

            for (let i = 0; i < count; i++) {
                const xPos = horizontalSpacing * (i + 1);
                let role;

                if (lineIndex === parts.length - 1) {
                    role = "Delantero";
                } else if (lineIndex === parts.length - 2) {
                    role = "Centrocampista";
                } else {
                    role = "Defensa";
                }

                positions.push({
                    x: xPos,
                    y: yPos,
                    role: role,
                    id: `pos-${positionCounter++}`,
                });
            }
        });

        return positions;
    }
}
