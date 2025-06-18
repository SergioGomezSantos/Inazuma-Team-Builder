export default class PlayerDragDrop {
    static init() {
        this.playerItems = document.querySelectorAll(".list-player");
        this.setupDragEvents();
    }

    static setupDragEvents() {
        this.playerItems.forEach((player) => {
            player.setAttribute("draggable", "true");
            player.addEventListener("dragstart", this.handleDragStart);
        });
    }

    static handleDragStart(e) {
        console.log("[Drag START] Dataset completo:", this.dataset);
        const playerId = this.dataset.playerId || this.id;
        console.log("[Drag START] ID a transferir:", playerId);

        e.dataTransfer.setData("text/plain", playerId);

        const fromPosition = this.closest(".field-position");
        if (fromPosition) {
            console.log(
                "[Drag START] Desde posición:",
                fromPosition.dataset.position
            );
            e.dataTransfer.setData(
                "from-position",
                fromPosition.dataset.position
            );
            e.dataTransfer.effectAllowed = "move";
        } else {
            console.log("[Drag START] Desde lista de jugadores");
            e.dataTransfer.effectAllowed = "copy";
        }

        if (this.querySelector("img")) {
            console.log("[Drag START] Configurando imagen de arrastre");
            e.dataTransfer.setDragImage(this.querySelector("img"), 50, 50);
        }
    }
}
