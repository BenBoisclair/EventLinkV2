// Create utils/logger.ts
export const logger = {
    log: (message: string, ...args: any[]) => {
        if (import.meta.env.DEV) {
            console.log(`[WebsiteBuilder] ${message}`, ...args);
        }
    },
    warn: (message: string, ...args: any[]) => {
        if (import.meta.env.DEV) {
            console.warn(`[WebsiteBuilder] ${message}`, ...args);
        }
    },
    error: (message: string, ...args: any[]) => {
        if (import.meta.env.DEV) {
            console.error(`[WebsiteBuilder] ${message}`, ...args);
        }
    },
};
