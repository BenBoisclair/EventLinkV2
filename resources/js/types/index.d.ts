export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    current_team?: {
        id: number;
        name?: string;
    };
}

import type { Event } from "./event";
import type { ExhibitorPagination } from "./exhibitor";
import type { ThemeColors, ThemeOptions, Widget } from "./websiteBuilder";

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
    event: Event;
    events: Event[];
    exhibitors?: ExhibitorPagination;
    flash?: {
        success?: string;
        error?: string;
    };
    websiteId?: string;

    websiteSettings?: {
        widgets?: Widget[];
        colorTheme?: {
            selectedTheme: ThemeOptions;
            themes: Record<ThemeOptions, ThemeColors>;
        };
    };
    isPublished?: boolean;
    lastUpdatedAt?: string | null;
};

declare global {
    interface Window {
        // ...
    }

    interface Error {
        response?: {
            data: {
                message: string;
            };
        };
    }
}

declare module "vue" {
    // ...
}

declare module "@inertiajs/core" {
    // ...
}

export {};
