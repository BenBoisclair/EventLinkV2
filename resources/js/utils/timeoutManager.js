export class TimeoutManager {
    constructor() {
        this.timeouts = new Map();
    }

    set(key, callback, delay) {
        this.clear(key);
        const id = setTimeout(() => {
            callback();
            this.timeouts.delete(key);
        }, delay);
        this.timeouts.set(key, id);
    }

    clear(key) {
        const id = this.timeouts.get(key);
        if (id) {
            clearTimeout(id);
            this.timeouts.delete(key);
        }
    }

    clearAll() {
        this.timeouts.forEach((id) => clearTimeout(id));
        this.timeouts.clear();
    }
}
