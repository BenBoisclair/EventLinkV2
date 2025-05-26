import Hotjar from "@hotjar/browser";
import "../css/app.css";
import "./bootstrap";
import axios from "axios";

// Ensure CSRF cookie is set before any authenticated API requests
axios.get("/sanctum/csrf-cookie");

import { createInertiaApp } from "@inertiajs/vue3";
import {
    mdiAccountGroupOutline,
    mdiAlertCircleOutline,
    mdiArrowBottomLeft,
    mdiArrowBottomRight,
    mdiArrowDown,
    mdiArrowLeft,
    mdiArrowRight,
    mdiArrowTopLeft,
    mdiArrowTopRight,
    mdiArrowUp,
    mdiArrowUpRight,
    mdiBrightness6,
    mdiCalendarBlankOutline,
    mdiCalendarOutline,
    mdiCellphone,
    mdiCheck,
    mdiCheckCircleOutline,
    mdiChevronDown,
    mdiChevronLeft,
    mdiChevronRight,
    mdiChevronUp,
    mdiCircleSmall,
    mdiClockOutline,
    mdiClose,
    mdiCloudOffOutline,
    mdiCloudUploadOutline,
    mdiCogOutline,
    mdiContentCopy,
    mdiContentSave,
    mdiCurrencyUsd,
    mdiDesktopTowerMonitor,
    mdiDomain,
    mdiEyeOutline,
    mdiFoodOutline,
    mdiInformationOutline,
    mdiLink,
    mdiListBox,
    mdiLoading,
    mdiLogin,
    mdiLogout,
    mdiMagnify,
    mdiMapMarkerOutline,
    mdiMicrophoneOutline,
    mdiMonitor,
    mdiPlus,
    mdiRefresh,
    mdiShareVariantOutline,
    mdiSquareEditOutline,
    mdiStoreOutline,
    mdiTablet,
    mdiTicketOutline,
    mdiTrashCanOutline,
    mdiUndo,
    mdiRedo,
    mdiViewGridOutline,
    mdiWeatherNight,
    mdiWeatherSunny,
    mdiWeb,
} from "@mdi/js";
import { createHead } from "@vueuse/head";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createPinia } from "pinia";
import { createApp, h } from "vue";
import { createVuetify } from "vuetify";
import VuetifyUseDialog from "vuetify-use-dialog";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { aliases, mdi } from "vuetify/iconsets/mdi-svg";
import "vuetify/styles";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { useThemeStore } from "./stores/darkmode";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

// Only initialize Hotjar in production
if (import.meta.env.PROD) {
    const siteId = 6393124;
    const hotjarVersion = 6;
    Hotjar.init(siteId, hotjarVersion);
}

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: "mdi",
        aliases: {
            ...aliases,
            accountGroupOutline: mdiAccountGroupOutline,
            alertCircleOutline: mdiAlertCircleOutline,
            arrowBottomLeft: mdiArrowBottomLeft,
            arrowBottomRight: mdiArrowBottomRight,
            arrowDown: mdiArrowDown,
            arrowLeft: mdiArrowLeft,
            arrowRight: mdiArrowRight,
            arrowTopLeft: mdiArrowTopLeft,
            arrowTopRight: mdiArrowTopRight,
            arrowUp: mdiArrowUp,
            brightness6: mdiBrightness6,
            calendarBlankOutline: mdiCalendarBlankOutline,
            calendarOutline: mdiCalendarOutline,
            cellphone: mdiCellphone,
            check: mdiCheck,
            checkCircleOutline: mdiCheckCircleOutline,
            chevronDown: mdiChevronDown,
            chevronLeft: mdiChevronLeft,
            chevronRight: mdiChevronRight,
            chevronUp: mdiChevronUp,
            circleSmall: mdiCircleSmall,
            clockOutline: mdiClockOutline,
            cloudOffOutline: mdiCloudOffOutline,
            cloudUploadOutline: mdiCloudUploadOutline,
            cogOutline: mdiCogOutline,
            contentSave: mdiContentSave,
            currencyUsd: mdiCurrencyUsd,
            desktopTowerMonitor: mdiDesktopTowerMonitor,
            domain: mdiDomain,
            eyeOutline: mdiEyeOutline,
            foodOutline: mdiFoodOutline,
            informationOutline: mdiInformationOutline,
            link: mdiLink,
            listBox: mdiListBox,
            loading: mdiLoading,
            login: mdiLogin,
            logout: mdiLogout,
            magnify: mdiMagnify,
            mapMarkerOutline: mdiMapMarkerOutline,
            microphoneOutline: mdiMicrophoneOutline,
            monitor: mdiMonitor,
            plus: mdiPlus,
            refresh: mdiRefresh,
            squareEditOutline: mdiSquareEditOutline,
            storeOutline: mdiStoreOutline,
            tablet: mdiTablet,
            ticketOutline: mdiTicketOutline,
            trashCanOutline: mdiTrashCanOutline,
            undo: mdiUndo,
            redo: mdiRedo,
            viewGridOutline: mdiViewGridOutline,
            weatherNight: mdiWeatherNight,
            weatherSunny: mdiWeatherSunny,
            web: mdiWeb,
            arrowUpRight: mdiArrowUpRight,
            shareVariantOutline: mdiShareVariantOutline,
            contentCopy: mdiContentCopy,
            close: mdiClose,
        },
        sets: { mdi },
    },
    theme: {
        defaultTheme: "light",
        themes: {
            light: {
                dark: false,
            },
            dark: {
                dark: true,
            },
        },
    },
});

const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const head = createHead();
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(VuetifyUseDialog)
            .use(pinia)
            .use(head);

        app.mount(el);

        // Initialize theme directly after mounting
        const themeStore = useThemeStore();
        themeStore.initializeTheme(vuetify);

        // Identify user in Hotjar (only in production and if user is logged in)
        if (import.meta.env.PROD) {
            // Assert the type of the auth prop
            const auth = props.initialPage.props.auth;
            const user = auth?.user;
            if (user && user.id) {
                Hotjar.identify(String(user.id), {
                    // Add any other user attributes you want to track here
                    name: user.name,
                    email: user.email,
                });
            }
        }
    },
    progress: {
        color: "#4B5563",
    },
});
