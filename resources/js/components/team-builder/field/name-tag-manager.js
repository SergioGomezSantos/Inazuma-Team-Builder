export class NameTagManager {
    static createNameTag(
        positionEl,
        { showNames, isBench, playerName, positionRole }
    ) {
        const existingTag = positionEl.querySelector(".player-name-tag");
        if (existingTag) existingTag.remove();

        const nameTag = document.createElement("div");
        nameTag.className = `player-name-tag absolute left-1/2 bottom-0 transform -translate-x-1/2 w-full text-center
            bg-white dark:bg-gray-800 shadow-md text-xs px-2 py-1 rounded-full whitespace-nowrap pointer-events-none
            transition-all duration-200 border-2 border-white dark:border-gray-600`;

        nameTag.style.cssText = `
            bottom: ${isBench ? "-1.2rem" : "-1.5rem"};
            display: ${showNames ? "block" : "none"};
            ${!playerName ? "color: transparent; user-select: none;" : ""}
        `;

        const displayText = playerName
            ? playerName.charAt(0).toUpperCase() + playerName.slice(1)
            : positionRole
            ? positionRole.charAt(0).toUpperCase() + positionRole.slice(1)
            : "&nbsp;";

        nameTag.textContent = displayText;
        positionEl.appendChild(nameTag);
        return nameTag;
    }
    static createEmptyNameTag(positionEl, { showNames, isBench }) {
        const existingTag = positionEl.querySelector(".player-name-tag");
        if (existingTag) existingTag.remove();

        const nameTag = document.createElement("div");
        nameTag.className = `player-name-tag absolute left-1/2 transform -translate-x-1/2 w-full text-center
            bg-white dark:bg-gray-800 shadow-md text-xs px-2 py-1 rounded-full whitespace-nowrap pointer-events-none
            transition-all duration-200 border-2 border-white dark:border-gray-600`;

        nameTag.style.cssText = `
            bottom: ${isBench ? "-1.2rem" : "-1.5rem"};
            display: ${showNames ? "block" : "none"};
            color: transparent;
            user-select: none;
        `;

        nameTag.innerHTML = "&nbsp;";
        positionEl.appendChild(nameTag);
        return nameTag;
    }

    static updateNameTag(positionEl, { showNames, playerName, positionRole }) {
        const nameTag = positionEl.querySelector(".player-name-tag");
        if (nameTag) {
            nameTag.style.display = showNames ? "block" : "none";
            if (playerName) {
                nameTag.textContent =
                    playerName.charAt(0).toUpperCase() + playerName.slice(1);
                nameTag.style.color = "";
                nameTag.style.userSelect = "";
            } else {
                nameTag.textContent = positionRole
                    ? positionRole.charAt(0).toUpperCase() +
                      positionRole.slice(1)
                    : "&nbsp;";
                nameTag.style.color = "transparent";
                nameTag.style.userSelect = "none";
            }
        } else {
            this.createNameTag(positionEl, {
                showNames,
                playerName,
                positionRole,
            });
        }
    }

    static updateAllNameTags(showNames) {
        document
            .querySelectorAll(".field-position, .bench-position")
            .forEach((positionEl) => {
                const playerImg = positionEl.querySelector(
                    "img[data-player-id]"
                );
                this.updateNameTag(positionEl, {
                    showNames,
                    playerName: playerImg?.getAttribute("data-player-name"),
                    positionRole: positionEl.dataset.positionRole,
                });
            });
    }
}
