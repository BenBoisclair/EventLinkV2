import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { PageProps as AppPageProps } from './';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    /* eslint-disable no-var */
    var route: typeof ziggyRoute;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
        $inertia: {
            visit: (url: string, options?: any) => void;
            get: (url: string, options?: any) => void;
            post: (url: string, options?: any) => void;
            put: (url: string, options?: any) => void;
            patch: (url: string, options?: any) => void;
            delete: (url: string, options?: any) => void;
        };
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
