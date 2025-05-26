import { SaveError, SaveErrorType } from '@/types/websiteBuilder';
import axios from 'axios';

/**
 * Classifies an error based on its type and content.
 * @param error - The error object to classify.
 * @param defaultMessage - The default message to use if the error cannot be classified.
 * @returns A SaveError object representing the error type and details.
 */
export function classifyApiError(
    error: any,
    defaultMessage = 'An unexpected error occurred',
): SaveError {
    if (
        error &&
        typeof error === 'object' &&
        'type' in error &&
        'message' in error
    ) {
        return {
            type: error.type as SaveErrorType,
            message: error.message,
            details: error.details ?? error,
        };
    }

    if (axios.isAxiosError(error)) {
        if (error.response) {
            const serverMessage = error.response.data?.message || error.message;

            switch (error.response.status) {
                case 400:
                    return {
                        type: SaveErrorType.VALIDATION,
                        message: `Operation failed: ${serverMessage}`,
                        details: error.response.data,
                    };
                case 401:
                case 403:
                    return {
                        type: SaveErrorType.PERMISSION,
                        message: 'You do not have permission for this action',
                        details: error.response.data,
                    };
                case 404:
                    return {
                        type: SaveErrorType.NOT_FOUND,
                        message: 'Resource not found',
                        details: error.response.data,
                    };
                default:
                    return {
                        type: SaveErrorType.UNKNOWN,
                        message: `Failed: ${serverMessage || 'Server error'} (${error.response.status})`,
                        details: error.response.data,
                    };
            }
        } else if (error.request) {
            return {
                type: SaveErrorType.NETWORK,
                message:
                    'Connection error. Please check your internet and try again.',
                details: error.message,
            };
        }
    }

    return {
        type: SaveErrorType.UNKNOWN,
        message: defaultMessage,
        details: String(error),
    };
}
