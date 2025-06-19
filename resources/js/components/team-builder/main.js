import LazyLoader from "./images-interaction/lazy-loader.js";
import PlayerSearch from "./player-search.js";
import PlayerDragDrop from "./dragdrop/player-dragdrop.js";
import FormationManager from "./formations/formation-manager.js";
import FieldRenderer from "./field/field-renderer.js";
import TeamManager from "./team/team-manager.js";
import ImageSelector from "./images-interaction/image-selector.js";
window.TeamManager = TeamManager;

const TeamBuilder = {
    init() {
        FieldRenderer.init();
        FormationManager.init();
        TeamManager.init();
        PlayerDragDrop.init();
        PlayerSearch.init();
        LazyLoader.init();
        ImageSelector.init();
    },
};

export default TeamBuilder;
