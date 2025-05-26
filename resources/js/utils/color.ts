/**
 * Calculates the luminance of a hex color.
 * Based on the WCAG formula: https://www.w3.org/TR/WCAG20/#relativeluminancedef
 *
 * @param hex - The hex color string (e.g., '#RRGGBB' or '#RGB').
 * @returns The relative luminance (0 to 1).
 */
export function getLuminance(hex: string): number {
    hex = hex.replace('#', '');

    // Handle shorthand hex (#RGB)
    if (hex.length === 3) {
        hex = hex
            .split('')
            .map((char) => char + char)
            .join('');
    }

    if (hex.length !== 6) {
        throw new Error('Invalid hex color format. Must be #RRGGBB or #RGB.');
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    const lum = [r, g, b].map((v) => {
        v /= 255;
        // sRGB EOTF function
        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });

    // Calculate luminance using the standard coefficients
    return 0.2126 * lum[0] + 0.7152 * lum[1] + 0.0722 * lum[2];
}

/**
 * Determines if a hex color is considered 'light' based on its luminance.
 *
 * @param hex - The hex color string.
 * @param threshold - The luminance threshold (default: 0.5). Colors above this are considered light.
 * @returns True if the color is light, false otherwise.
 */
export function isColorLight(hex: string, threshold: number = 0.5): boolean {
    try {
        return getLuminance(hex) > threshold;
    } catch (e) {
        console.error(`Error determining if color ${hex} is light:`, e);
        // Default to assuming dark for safety if calculation fails
        return false;
    }
}

/**
 * Converts a hex color to RGB values
 * @param hex - The hex color string (with or without #)
 * @returns Object with r, g, b values or null if invalid
 */
export function hexToRgb(hex: string): { r: number; g: number; b: number } | null {
    // Remove # if present
    hex = hex.replace("#", "");
    
    // Validate hex length
    if (hex.length !== 6) return null;
    
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    
    // Check if parsing was successful
    if (isNaN(r) || isNaN(g) || isNaN(b)) return null;
    
    return { r, g, b };
}

/**
 * Converts hex color to RGBA string
 * @param hex - The hex color string
 * @param alpha - The alpha value (0-1)
 * @returns RGBA color string
 */
export function hexToRgba(hex: string, alpha: number = 1): string {
    const rgb = hexToRgb(hex);
    if (!rgb) return `rgba(0, 0, 0, ${alpha})`;
    return `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha})`;
}

/**
 * Calculates the brightness of a color using a simpler formula
 * @param hex - The hex color string
 * @returns Brightness value (0-255)
 */
export function getColorBrightness(hex: string): number {
    const rgb = hexToRgb(hex);
    if (!rgb) return 0;
    // Using the relative luminance formula
    return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
}

/**
 * Gets a contrasting text color (black or white) based on background color
 * @param backgroundColor - The background color in hex format
 * @param lightColor - The color to use for light backgrounds (default: #000000)
 * @param darkColor - The color to use for dark backgrounds (default: #FFFFFF)
 * @returns The contrasting color
 */
export function getContrastingTextColor(
    backgroundColor: string,
    lightColor: string = "#000000",
    darkColor: string = "#FFFFFF"
): string {
    return getColorBrightness(backgroundColor) > 128 ? lightColor : darkColor;
}

/**
 * Converts RGBA string to hex color (ignores alpha)
 * @param rgba - The RGBA color string
 * @returns Hex color string
 */
export function rgbaToHex(rgba: string): string {
    const match = rgba.match(
        /rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/
    );
    if (!match) return "#000000";
    
    const r = parseInt(match[1]).toString(16).padStart(2, "0");
    const g = parseInt(match[2]).toString(16).padStart(2, "0");
    const b = parseInt(match[3]).toString(16).padStart(2, "0");
    
    return `#${r}${g}${b}`;
}
