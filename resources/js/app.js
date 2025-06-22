import "./bootstrap";
import Alpine from "alpinejs";
import Sortable from "sortablejs";

// Carga condicional del team builder
if (document.querySelector(".soccer-field")) {
    import("./components/team-builder/main").then((module) => {
        module.default.init();
    });
}

window.Alpine = Alpine;
window.Sortable = Sortable;
Alpine.start();
