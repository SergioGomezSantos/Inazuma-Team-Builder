import LazyLoader from "./lazy-loader.js";
import PlayerSearch from "./player-search.js";
import PlayerDragDrop from "./player-dragdrop.js";
import FormationManager from "./formation-manager.js";
import FieldRenderer from "./field-renderer.js";
import TeamManager from "./team-manager.js";
import ImageSelector from "./image-selector.js";
window.TeamManager = TeamManager;

const TeamBuilder = {
    init() {
        LazyLoader.init();
        PlayerSearch.init();
        PlayerDragDrop.init();
        FormationManager.init();
        TeamManager.init();
        ImageSelector.init();
    },
};

export default TeamBuilder;
