export default class ImageSelector {
    static init() {
        this.emblemSelect = document.getElementById("emblem-select");
        this.emblemImage = document.getElementById("emblem-image");
        this.coachSelect = document.getElementById("coach-select");
        this.coachImage = document.getElementById("coach-image");
        this.clearTeamBtn = document.getElementById("clear-team");
        this.randomTeamBtn = document.getElementById("random-team");

        this.setupEventListeners();
    }

    static setupEventListeners() {
        // Eventos para cambios manuales
        this.emblemSelect?.addEventListener("change", (e) =>
            this.updateEmblem()
        );
        this.coachSelect?.addEventListener("change", (e) => this.updateCoach());

        // Integración con botones existentes
        this.clearTeamBtn?.addEventListener("click", () =>
            this.resetSelections()
        );
        this.randomTeamBtn?.addEventListener("click", () =>
            this.randomizeSelections()
        );
    }

    static updateEmblem() {
        const selectedOption =
            this.emblemSelect.options[this.emblemSelect.selectedIndex];
        const imagePath = selectedOption?.getAttribute("data-image");
        const emblemName = selectedOption?.textContent.trim();
        console.log(emblemName);
        this.updateImage(this.emblemImage, imagePath, "emblems");

        // Map - EmblemHelper PHP
        const sizeMap = {
            "Mar de Árboles": "w-24 h-24 pt-4",
            "Inazuma Japón": "w-20 h-24 pt-4",
            Raimon: "w-24 h-24 pt-4",
            Zeus: "w-24 h-24 pt-4",
            Umbrella: "w-24 h-24 pt-4",
            Sallys: "w-24 h-24 pt-4",
            "Raimon 2": "w-24 h-24 pt-4",
            "Servicio Secreto": "w-24 h-24 pt-4",
            Caos: "w-24 h-24 pt-4",
            Génesis: "w-28 h-28",
            "Robots Guardias": "w-24 h-24 pt-4",
            "Jóvenes Inazuma": "w-24 h-24 pt-4",
            "Leones del Desierto": "w-24 h-24 pt-4",
            "Knights of Queen": "w-24 h-24 pt-4",
            Unicorn: "w-24 h-24 pt-4",
            "The Little Giants": "w-24 h-24 pt-4",
            "Los Rojos": "w-24 h-24 pt-4",
            "Brocken Brigade": "w-24 h-24 pt-4",
            "Grifos de la Rosa": "w-24 h-24 pt-4",
            "Caimanes del Cabo": "w-24 h-24 pt-4",
            "Equipo Ogro": "w-24 h-24 pt-4",
            "Equipo D": "w-28 h-28",
            "Zoolan Team": "w-24 h-24 pt-4",
            "Sky Team": "w-24 h-24 pt-4",
            "Dark Team": "w-24 h-24 pt-4",
            default: "w-32 h-32",
        };

        const sizeClasses = sizeMap[emblemName] || sizeMap["default"];

        const allSizeClasses = [
            "w-24",
            "h-24",
            "w-28",
            "h-28",
            "w-32",
            "h-32",
            "pt-4",
        ];

        allSizeClasses.forEach((cls) => this.emblemImage.classList.remove(cls));

        sizeClasses.split(" ").forEach((cls) => {
            if (cls) this.emblemImage.classList.add(cls);
        });
    }

    static updateCoach() {
        const selectedOption =
            this.coachSelect.options[this.coachSelect.selectedIndex];
        const imagePath = selectedOption?.getAttribute("data-image");
        this.updateImage(this.coachImage, imagePath, "coaches");
    }

    static updateImage(imgElement, imagePath, folder) {
        if (!imgElement || !imagePath) return;

        const tempImg = new Image();
        tempImg.src = `/storage/${folder}/${imagePath}`;

        tempImg.onload = () => {
            imgElement.src = tempImg.src;
            imgElement.style.opacity = "0";
            setTimeout(() => {
                imgElement.style.transition = "opacity 0.3s ease";
                imgElement.style.opacity = "1";
            }, 10);
        };

        tempImg.onerror = () => {
            console.error(`Error loading image: ${imagePath}`);
            imgElement.src = `/storage/${folder}/placeholder.png`;
        };
    }

    static resetSelections() {
        // Resetear a las primeras opciones
        if (this.emblemSelect) {
            this.emblemSelect.selectedIndex = 0;
            this.updateEmblem();
        }

        if (this.coachSelect) {
            this.coachSelect.selectedIndex = 0;
            this.updateCoach();
        }
    }

    static randomizeSelections() {
        // Randomizar emblema
        if (this.emblemSelect && this.emblemSelect.options.length > 0) {
            const randomIndex = Math.floor(
                Math.random() * this.emblemSelect.options.length
            );
            this.emblemSelect.selectedIndex = randomIndex;
            this.updateEmblem();
        }

        // Randomizar entrenador
        if (this.coachSelect && this.coachSelect.options.length > 0) {
            const randomIndex = Math.floor(
                Math.random() * this.coachSelect.options.length
            );
            this.coachSelect.selectedIndex = randomIndex;
            this.updateCoach();
        }
    }
}
