import { computed, ComputedRef } from "vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { storeToRefs } from "pinia";

interface ThemeColors {
    primary: string;
    secondary: string;
    accent: string;
    background: string;
}

/**
 * Converts hex color to RGB values
 */
function hexToRgb(hex: string): { r: number; g: number; b: number } | null {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
        ? {
              r: parseInt(result[1], 16),
              g: parseInt(result[2], 16),
              b: parseInt(result[3], 16),
          }
        : null;
}

/**
 * Calculates luminance of a color for contrast calculations
 */
function getLuminance(r: number, g: number, b: number): number {
    const [rs, gs, bs] = [r, g, b].map((c) => {
        c = c / 255;
        return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
}

/**
 * Gets contrasting text color (black or white) based on background
 */
function getContrastingTextColor(backgroundColor: string): string {
    const rgb = hexToRgb(backgroundColor);
    if (!rgb) return "#000000";

    const luminance = getLuminance(rgb.r, rgb.g, rgb.b);
    return luminance > 0.5 ? "#000000" : "#ffffff";
}

/**
 * Adds alpha transparency to a hex color
 */
function addAlpha(hexColor: string, alpha: number): string {
    const rgb = hexToRgb(hexColor);
    if (!rgb) return hexColor;

    return `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha})`;
}

/**
 * Composable that provides semantic color mappings based on the current theme
 * Can work with either theme props (for public view) or store (for builder)
 * 
 * @param themeProps - Optional theme colors to use instead of store
 */
export function useThemeColors(themeProps?: ThemeColors) {
    // If theme is passed as props, use it directly
    // Otherwise, fall back to store (for builder context)
    let theme: ComputedRef<ThemeColors>;
    
    if (themeProps) {
        theme = computed(() => themeProps);
    } else {
        const websiteBuilderStore = useWebsiteBuilderStore();
        const { theme: storeTheme } = storeToRefs(websiteBuilderStore);
        theme = storeTheme as ComputedRef<ThemeColors>;
    }

    // Semantic color mappings that update reactively with theme changes
    const colors = computed(() => {
        const currentTheme = theme.value;

        return {
            // Primary colors for buttons and important elements
            buttonPrimary: currentTheme.primary,
            buttonPrimaryText: getContrastingTextColor(currentTheme.primary),
            linkColor: currentTheme.primary,
            highlightColor: currentTheme.primary,

            // Secondary colors for less prominent elements
            buttonSecondary: currentTheme.secondary,
            buttonSecondaryText: getContrastingTextColor(
                currentTheme.secondary
            ),
            borderColor: addAlpha(currentTheme.secondary, 0.3),
            mutedText: currentTheme.secondary,

            // Accent colors for special highlights
            accentBackground: currentTheme.accent,
            accentText: getContrastingTextColor(currentTheme.accent),
            warningColor: currentTheme.accent,

            // Background colors
            backgroundPrimary: currentTheme.background,
            backgroundSecondary: addAlpha(currentTheme.secondary, 0.05),
            overlayBackground: addAlpha(currentTheme.primary, 0.8),
            overlayText: getContrastingTextColor(
                addAlpha(currentTheme.primary, 0.8)
            ),

            // Text colors with automatic contrast
            textPrimary: getContrastingTextColor(currentTheme.background),
            textSecondary: currentTheme.secondary,
            textOnPrimary: getContrastingTextColor(currentTheme.primary),
            textOnAccent: getContrastingTextColor(currentTheme.accent),

            // Input and form colors
            inputBackground: currentTheme.background,
            inputBorder: addAlpha(currentTheme.secondary, 0.3),
            inputFocus: currentTheme.primary,
            inputText: getContrastingTextColor(currentTheme.background),

            // Card and section colors
            cardBackground: currentTheme.background,
            cardBorder: addAlpha(currentTheme.secondary, 0.2),
            sectionBackground: currentTheme.background,

            // Raw theme colors (for edge cases)
            themePrimary: currentTheme.primary,
            themeSecondary: currentTheme.secondary,
            themeAccent: currentTheme.accent,
            themeBackground: currentTheme.background,
        };
    });

    // Utility functions
    const utils = {
        /**
         * Get contrasting text color for any background
         */
        getContrastingTextColor,

        /**
         * Add alpha transparency to any color
         */
        addAlpha,

        /**
         * Convert hex to RGB
         */
        hexToRgb,

        /**
         * Get a custom color with alpha from theme
         */
        getThemeColorWithAlpha: (
            colorKey: keyof ThemeColors,
            alpha: number
        ) => {
            return addAlpha(theme.value[colorKey], alpha);
        },
    };

    return {
        colors,
        utils,
        // Direct access to theme for edge cases
        theme,
    };
}
