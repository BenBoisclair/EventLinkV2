import axios from 'axios';

/**
 * Ensures a fresh CSRF cookie is available
 * @returns {Promise<void>}
 */
export async function ensureCsrfCookie() {
    try {
        await axios.get('/sanctum/csrf-cookie');
        
        // Update the CSRF token in axios headers
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    } catch (error) {
        // Silently handle CSRF cookie fetch errors
        // Detailed error logging should be done server-side
    }
}

/**
 * Updates the CSRF token from the meta tag
 */
export function updateCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
}