import forms from "@tailwindcss/forms";
import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#193CB8",
                "brand-secondary": "#8200DB",
                accent: "#162456",
                border: "#E2E8F0",
                danger: "#FB2C36",
                success: "#00C16A",
                warning: "#EFB100",
                toned: "#45556c",
                "text-muted": "#62748E",
                "text-dimmed": "#90A1B9",
                "text-toned": "#45556C",
                "status-blue": "#193CB8",
                "status-yellow": "#EFB100",
                "status-red": "#FB2C36",
                "status-green": "#00C16A",
                surface: "#F1F5F9",

                // Dark Mode Colors
                "dark-background": "#0F1419",
                "dark-surface": "#1A1F26",
                "dark-surface-elevated": "#242A32",
                "dark-border": "#2D3642",
                "dark-text-primary": "#E2E8F0",
                "dark-text-secondary": "#94A3B8",
                "dark-text-tertiary": "#64748B",
                "dark-primary": "#4C6EF5",
                "dark-primary-dimmed": "#364FC7",
                "dark-accent": "#2B3A5E",
                "dark-status-blue": "#4C6EF5",
                "dark-status-yellow": "#F0B132",
                "dark-status-red": "#FF4C56",
                "dark-status-green": "#2FBF71",
            },
            borderRadius: {
                button: "6px",
                card: "8px",
            },
            spacing: {
                section: "1.5rem", // 24px
                component: "1.5rem", // 24px
                small: "1rem", // 16px
                tiny: "0.5rem", // 8px
            },
            maxWidth: {
                input: "100%",
                image: "450px",
                "side-panel": "158px",
            },
            height: {
                "status-badge": "1.25rem", // 20px
            },
            width: {
                "status-badge": "1.25rem", // 20px
            },
            fontSize: {
                "heading-primary": ["36px", { lineHeight: "1.2" }],
                "heading-secondary": ["24px", { lineHeight: "1.2" }],
                body: ["14px", { lineHeight: "1.5" }],
            },
        },
    },

    safelist: [
        {
            pattern:
                /bg-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)/,
        },
        "bg-white",
        "bg-black",
    ],

    plugins: [forms],
};
