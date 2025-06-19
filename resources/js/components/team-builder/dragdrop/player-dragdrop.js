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
        const playerId = this.dataset.playerId || this.id;

        e.dataTransfer.setData("text/plain", playerId);

        const fromPosition = this.closest(".field-position");
        if (fromPosition) {
            e.dataTransfer.setData(
                "from-position",
                fromPosition.dataset.position
            );
            e.dataTransfer.effectAllowed = "move";
        } else {
            e.dataTransfer.effectAllowed = "copy";
        }

        if (this.querySelector("img")) {
            e.dataTransfer.setDragImage(this.querySelector("img"), 50, 50);
        }
    }
}
