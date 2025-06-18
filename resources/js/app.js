import "./bootstrap";
import Alpine from "alpinejs";

// Carga condicional del team builder
if (document.querySelector(".soccer-field")) {
    import("./components/team-builder/main").then((module) => {
        module.default.init();
    });
}

window.Alpine = Alpine;
Alpine.start();
