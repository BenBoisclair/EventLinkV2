import { onMounted, onUnmounted } from "vue";

/**
 * Sets up Echo event listeners for the website builder.
 * @param websiteId - The website ID to listen for events on.
 * @param onBlockImageProcessed - Callback for when any block image is processed.
 */
export function useWebsiteBuilderEcho(
    websiteId: number | null,
    onBlockImageProcessed: (eventData: {
        blockId: string;
        imageUrl: string;
        propName: string;
    }) => void
) {
    onMounted(() => {
        if (websiteId) {
            const channelName = `website.${websiteId}`;
            const eventName = "block.image.processed";

            console.log(
                `Listening on channel: ${channelName} for event: ${eventName}`
            );

            try {
                if (window.Echo) {
                    window.Echo.private(channelName).listen(
                        eventName,
                        (eventData: { blockId: string; imageUrl: string; propName: string }) => {
                            console.log(
                                "BlockImageProcessed event received:",
                                eventData
                            );
                            onBlockImageProcessed(eventData);
                        }
                    );
                } else {
                    console.error(
                        "Echo instance not found. Ensure bootstrap.js configures Echo globally."
                    );
                }
            } catch (error) {
                console.error("Error setting up Echo listener:", error);
            }
        } else {
            console.warn(
                "Website ID not available, cannot set up Echo listener."
            );
        }
    });

    onUnmounted(() => {
        if (websiteId && window.Echo) {
            const channelName = `website.${websiteId}`;
            console.log(`Leaving channel: ${channelName}`);
            window.Echo.leave(channelName);
        }
    });
}
