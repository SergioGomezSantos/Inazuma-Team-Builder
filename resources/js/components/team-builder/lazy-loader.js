export default class LazyLoader {
    static observer = null;

    static init() {
        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            {
                rootMargin: "200px",
                threshold: 0.01,
            }
        );
        this.observeVisiblePlayers();
    }

    static handleIntersection(entries) {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.onload = () => {
                        img.removeAttribute("data-src");
                        this.observer.unobserve(img);
                    };
                    img.onerror = () => {
                        img.src = "/storage/players/placeholder.png";
                        img.removeAttribute("data-src");
                        this.observer.unobserve(img);
                    };
                }
            }
        });
    }

    static observeVisiblePlayers() {
        document
            .querySelectorAll(
                '.list-player:not([style*="display: none"]) img[data-src]'
            )
            .forEach((img) => {
                this.observer.observe(img);
            });
    }

    static refresh() {
        this.observer.disconnect();
        this.observeVisiblePlayers();
    }
}
