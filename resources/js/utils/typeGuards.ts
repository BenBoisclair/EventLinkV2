import {
    DesignSettingsShape,
    ThemeSettingsShape,
} from '@/types/websiteBuilder';

/**
 * Type guard to validate the structure and types of theme settings.
 * @param settings - The settings object to validate.
 * @returns True if the settings object matches the ThemeSettingsShape.
 */
export function isValidThemeSettings(
    settings: any,
): settings is ThemeSettingsShape {
    if (typeof settings !== 'object' || settings === null) {
        return false;
    }
    // Optional: selectedTheme must be a string if present
    if (
        settings.selectedTheme !== undefined &&
        typeof settings.selectedTheme !== 'string'
    ) {
        return false;
    }
    // Optional: themes must be an object if present
    if (settings.themes !== undefined) {
        if (typeof settings.themes !== 'object' || settings.themes === null) {
            return false;
        }
        // Optional: Further validation of theme structure within themes object
        for (const key in settings.themes) {
            if (
                typeof settings.themes[key] !== 'object' ||
                settings.themes[key] === null
            ) {
                return false; // Each theme must be an object
            }
            // Check for required color properties (example)
            if (
                typeof settings.themes[key].primary !== 'string' ||
                typeof settings.themes[key].secondary !== 'string'
            ) {
                // console.warn(`Theme "${key}" is missing required color properties.`);
                // Depending on strictness, you might return false here or allow partial themes
            }
        }
    }
    return true;
}

/**
 * Type guard to validate the structure and types of design settings.
 * @param settings - The settings object to validate.
 * @returns True if the settings object matches the DesignSettingsShape.
 */
export function isValidDesignSettings(
    settings: any,
): settings is DesignSettingsShape {
    if (typeof settings !== 'object' || settings === null) {
        return false;
    }
    if (
        settings.selectedFont !== undefined &&
        typeof settings.selectedFont !== 'string'
    ) {
        return false;
    }
    if (settings.availableFonts !== undefined) {
        if (
            !Array.isArray(settings.availableFonts) ||
            !settings.availableFonts.every((f: any) => typeof f === 'string')
        ) {
            return false;
        }
    }
    return true;
}
