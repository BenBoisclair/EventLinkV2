/**
 * Formats a date string into a localized date string.
 * Returns an empty string if the input is null, undefined, or invalid.
 * @param dateString - The date string to format (e.g., "2023-10-27T10:00:00Z")
 * @returns Localized date string (e.g., "10/27/2023") or empty string.
 */
export function formatDate(dateString: string | null | undefined): string {
    if (!dateString) {
        return '';
    }
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return '';
        }
        return date.toLocaleDateString();
    } catch (error) {
        console.error('Error formatting date:', error);
        return '';
    }
}

/**
 * Formats a date string into a relative time string (e.g., "5 minutes ago", "in 2 hours").
 * Returns an empty string if the input is null, undefined, or invalid.
 * @param dateString - The date string to format (e.g., "2023-10-27T10:00:00Z")
 * @returns Relative time string or empty string.
 */
export function formatRelativeTime(
    dateString: string | null | undefined,
): string {
    if (!dateString) {
        return '';
    }

    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return '';
        }

        const now = new Date();
        const diffInSeconds = Math.round(
            (now.getTime() - date.getTime()) / 1000,
        );

        const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

        const units: { [key: string]: number } = {
            year: 31536000,
            month: 2592000,
            week: 604800,
            day: 86400,
            hour: 3600,
            minute: 60,
            second: 1,
        };

        for (const unit in units) {
            if (Math.abs(diffInSeconds) >= units[unit]) {
                const value = Math.round(diffInSeconds / units[unit]);
                return rtf.format(-value, unit as Intl.RelativeTimeFormatUnit);
            }
        }

        return rtf.format(0, 'second');
    } catch (error) {
        console.error('Error formatting relative time:', error);
        return '';
    }
}
