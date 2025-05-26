/**
 * Resolves a relative storage path (local or S3) to a full, usable URL.
 *
 * @param path The relative path stored in the database (e.g., 'exhibitor/logo/1-hexlabs.png').
 * @returns The full URL for the resource.
 */
export const resolveStorageUrl = (path: string | null | undefined): string => {
    if (!path) return '';

    // Check if it's already a full URL
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    // Check if S3 URL is configured in the frontend environment and use it
    const s3BaseUrl = import.meta.env.VITE_S3_URL;
    if (s3BaseUrl) {
        // Ensure no double slashes
        const trimmedBase = s3BaseUrl.endsWith('/')
            ? s3BaseUrl.slice(0, -1)
            : s3BaseUrl;
        const trimmedPath = path.startsWith('/') ? path.substring(1) : path;
        return `${trimmedBase}/${trimmedPath}`;
    }

    // Otherwise, assume local public storage linked at /storage/
    // Ensure no double slashes if path somehow starts with /
    const trimmedPath = path.startsWith('/') ? path.substring(1) : path;
    return `/storage/${trimmedPath}`;
};
