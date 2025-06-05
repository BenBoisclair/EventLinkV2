import axios from "axios";
import { route } from "ziggy-js";
import type { Block, Theme } from "@/types/websiteBuilder";

interface SaveWebsiteResponse {
    message: string;
    blocks: Block[];
    updated_at?: string;
}

/**
 * Saves the website blocks and theme for a given website ID.
 * @param websiteId The ID of the website to save.
 * @param blocks The array of Block objects.
 * @param theme The theme configuration.
 * @returns A promise that resolves with the save response containing updated blocks.
 */
export async function saveWebsiteData(
    eventId: number,
    websiteId: number,
    blocks: Block[],
    theme: Theme
): Promise<SaveWebsiteResponse> {
    try {
        if (!eventId) {
            throw new Error("Missing Event ID for save operation");
        }

        if (!websiteId) {
            throw new Error("Missing Website ID for save operation");
        }

        // Check if any blocks have pending files
        const hasFiles = blocks.some(block => {
            return Object.keys(block.props).some(key => 
                key.startsWith('_pendingFile_') && block.props[key] instanceof File
            );
        });
        
        if (hasFiles) {
            // Use FormData to send files
            const formData = new FormData();
            
            blocks.forEach((block, index) => {
                formData.append(`blocks[${index}][id]`, block.id);
                formData.append(`blocks[${index}][type]`, block.type);
                
                // Append all props
                Object.keys(block.props).forEach(key => {
                    const value = block.props[key];
                    if (value !== null && value !== undefined) {
                        if (key.startsWith('_pendingFile_') && value instanceof File) {
                            // Append files
                            formData.append(`blocks[${index}][props][${key}]`, value);
                        } else if (!key.startsWith('_pendingFile_')) {
                            // Skip blob URLs - they're just temporary previews
                            if (typeof value === 'string' && value.startsWith('blob:')) {
                                return;
                            }
                            // Append other props as strings
                            formData.append(`blocks[${index}][props][${key}]`, String(value));
                        }
                    }
                });
            });

            // Append theme data
            formData.append('theme[primary]', theme.primary);
            formData.append('theme[secondary]', theme.secondary);
            formData.append('theme[accent]', theme.accent);
            formData.append('theme[background]', theme.background);

            const response = await axios.post<SaveWebsiteResponse>(
                route("website.builder.save", {
                    event: eventId,
                    website: websiteId,
                }),
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                }
            );
            return response.data;
        } else {
            // Regular JSON request - filter out blob URLs
            const cleanedBlocks = blocks.map(block => ({
                ...block,
                props: Object.fromEntries(
                    Object.entries(block.props).filter(([, value]) => {
                        // Exclude blob URLs
                        if (typeof value === 'string' && value.startsWith('blob:')) {
                            return false;
                        }
                        return true;
                    })
                )
            }));
            
            const response = await axios.post<SaveWebsiteResponse>(
                route("website.builder.save", {
                    event: eventId,
                    website: websiteId,
                }),
                {
                    blocks: cleanedBlocks,
                    theme: theme,
                }
            );
            return response.data;
        }
    } catch (error) {
        console.error("API Error saving website data:", error);
        throw error;
    }
}


/**
 * Creates a new website for a given event.
 * @param eventId The ID of the event.
 * @returns Promise with the response data (new website info).
 */
export async function createWebsite(eventId: number) {
    if (!eventId) throw new Error("Missing Event ID for website creation");
    const response = await axios.post(
        route("organiser.event.website.store", { event: eventId })
    );
    return response.data;
}

/**
 * Deletes a website for a given event.
 * @param eventId The ID of the event.
 * @param websiteId The ID of the website.
 * @returns Promise with the response data.
 */
export async function deleteWebsite(eventId: number, websiteId: number) {
    if (!eventId || !websiteId)
        throw new Error("Missing Event or Website ID for deletion");
    const response = await axios.delete(
        route("organiser.event.website.destroy", {
            event: eventId,
            website: websiteId,
        })
    );
    return response.data;
}

/**
 * Publishes a website for a given event.
 * @param eventId The ID of the event.
 * @param websiteId The ID of the website.
 * @returns Promise with the response data.
 */
export async function publishWebsite(eventId: number, websiteId: number) {
    if (!eventId || !websiteId)
        throw new Error("Missing Event or Website ID for publish operation");
    const response = await axios.post(
        route("website.builder.publish", {
            event: eventId,
            website: websiteId,
        })
    );
    return response.data;
}

/**
 * Unpublishes a website for a given event.
 * @param eventId The ID of the event.
 * @param websiteId The ID of the website.
 * @returns Promise with the response data.
 */
export async function unpublishWebsite(eventId: number, websiteId: number) {
    if (!eventId || !websiteId)
        throw new Error("Missing Event or Website ID for unpublish operation");
    const response = await axios.post(
        route("website.builder.unpublish", {
            event: eventId,
            website: websiteId,
        })
    );
    return response.data;
}
