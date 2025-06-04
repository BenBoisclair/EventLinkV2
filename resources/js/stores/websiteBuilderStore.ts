import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { Block, DeviceType } from "@/types/websiteBuilder";
import type { EventType } from "@/types/event";
import {
    saveWebsiteData,
    publishWebsite,
    unpublishWebsite,
} from "@/services/websiteBuilder";

// --- Store Definition ---
export const useWebsiteBuilderStore = defineStore("websiteBuilder", () => {
    const blocks = ref<Block[]>([]);
    const websiteId = ref<number | null>(null);
    const websiteSlug = ref<string | null>(null);
    const isPublished = ref<boolean>(false);
    const saveState = ref<"idle" | "saving" | "saved" | "error">("idle");
    const isDirty = ref<boolean>(false);
    const saveError = ref<string | null>(null);
    const currentEvent = ref<EventType | null>(null);
    const previewDevice = ref<DeviceType>("desktop");
    const undoStack = ref<Block[][]>([]);
    const redoStack = ref<Block[][]>([]);
    const currentBlockId = ref<string | null>(null);
    const editingBlockProps = ref<any | null>(null);
    const lastUpdatedAt = ref<string | null>(null);
    const autoSaveInterval = ref<number | null>(null);
    const showSavedMessage = ref<boolean>(false);
    const currentBlockType = computed(() => {
        if (!currentBlockId.value) return null;
        const block = blocks.value.find((b) => b.id === currentBlockId.value);
        return block ? block.type : null;
    });

    // --- Actions ---

    /**
     * Starts auto-save functionality that saves every 1 minute if there are unsaved changes.
     */
    function startAutoSave() {
        stopAutoSave(); // Clear any existing interval
        
        autoSaveInterval.value = window.setInterval(async () => {
            if (isDirty.value && saveState.value !== "saving") {
                console.log("Auto-saving website...");
                await saveWebsite();
            }
        }, 60000); // 60 seconds = 1 minute
    }

    /**
     * Stops auto-save functionality.
     */
    function stopAutoSave() {
        if (autoSaveInterval.value) {
            clearInterval(autoSaveInterval.value);
            autoSaveInterval.value = null;
        }
    }

    /**
     * Initializes the builder with data from backend or defaults.
     */
    function initializeBuilder(data: {
        websiteId: number | null;
        websiteSlug?: string | null;
        blocks: Block[];
        isPublished?: boolean;
        event?: EventType;
        lastUpdatedAt?: string;
    }) {
        websiteId.value = data.websiteId;
        websiteSlug.value = data.websiteSlug ?? null;
        blocks.value = Array.isArray(data.blocks) ? data.blocks : [];
        isPublished.value = data.isPublished ?? false;
        currentEvent.value = data.event ?? null;
        lastUpdatedAt.value = data.lastUpdatedAt ?? null;
        isDirty.value = false;
        saveState.value = "idle";
        saveError.value = null;
        undoStack.value = [];
        redoStack.value = [];
        startAutoSave();
    }

    /**
     * Adds a new block of the specified type.
     */
    function addBlock(blockType: Block["type"], initialProps: any = {}) {
        pushUndo();
        const newBlock: Block = {
            id: `${blockType}-${Date.now()}`,
            type: blockType,
            props: initialProps,
        };
        blocks.value.push(newBlock);
        isDirty.value = true;
        redoStack.value = [];
    }

    /**
     * Deletes a block by ID.
     */
    function deleteBlock(blockId: string) {
        pushUndo();
        blocks.value = blocks.value.filter((block) => block.id !== blockId);
        isDirty.value = true;
        redoStack.value = [];
    }

    /**
     * Updates properties of a block by ID.
     */
    function updateBlock(blockId: string, newProps: any) {
        pushUndo();
        const idx = blocks.value.findIndex((b) => b.id === blockId);
        if (idx !== -1) {
            blocks.value[idx] = {
                ...blocks.value[idx],
                props: { ...blocks.value[idx].props, ...newProps },
            };
            isDirty.value = true;
            redoStack.value = [];
        }
    }

    /**
     * Saves the website (blocks) to the backend.
     */
    async function saveWebsite() {
        if (!websiteId.value || !currentEvent.value) {
            saveError.value = "Website or event context missing.";
            return false;
        }
        if (saveState.value === "saving") return false;
        saveState.value = "saving";
        saveError.value = null;
        try {
            const response = await saveWebsiteData(
                currentEvent.value.id,
                websiteId.value,
                blocks.value
            );

            // Optionally update blocks from response
            if (response && Array.isArray(response.blocks)) {
                blocks.value = response.blocks;
            }
            if (response && response.updated_at) {
                lastUpdatedAt.value = response.updated_at;
            }
            saveState.value = "saved";
            isDirty.value = false;
            showSavedMessage.value = true;
            setTimeout(() => {
                if (saveState.value === "saved") saveState.value = "idle";
            }, 1200);
            setTimeout(() => {
                showSavedMessage.value = false;
            }, 3000);
            return true;
        } catch (error: any) {
            saveError.value = error?.message || "Failed to save website.";
            saveState.value = "error";
            return false;
        }
    }

    /**
     * Publishes the website.
     */
    async function publishWebsiteAction() {
        if (!websiteId.value || !currentEvent.value) return false;
        try {
            await publishWebsite(currentEvent.value.id, websiteId.value);
            isPublished.value = true;
            return true;
        } catch (error: any) {
            saveError.value = error?.message || "Failed to publish website.";
            return false;
        }
    }

    /**
     * Unpublishes the website.
     */
    async function unpublishWebsiteAction() {
        if (!websiteId.value || !currentEvent.value) return false;
        try {
            await unpublishWebsite(currentEvent.value.id, websiteId.value);
            isPublished.value = false;
            return true;
        } catch (error: any) {
            saveError.value = error?.message || "Failed to unpublish website.";
            return false;
        }
    }

    /**
     * Sets the preview device type.
     */
    function setPreviewDevice(device: DeviceType) {
        previewDevice.value = device;
    }

    /**
     * Undo the last block change.
     */
    function undo() {
        if (undoStack.value.length === 0) return;
        redoStack.value.push([...blocks.value]);
        const prev = undoStack.value.pop();
        if (prev) {
            blocks.value = [...prev];
            isDirty.value = true;
        }
    }

    /**
     * Redo the last undone block change.
     */
    function redo() {
        if (redoStack.value.length === 0) return;
        undoStack.value.push([...blocks.value]);
        const next = redoStack.value.pop();
        if (next) {
            blocks.value = [...next];
            isDirty.value = true;
        }
    }

    /**
     * Pushes the current blocks state to the undo stack.
     */
    function pushUndo() {
        undoStack.value.push(
            blocks.value.map((b) => ({ ...b, props: { ...b.props } }))
        );
        if (undoStack.value.length > 50) undoStack.value.shift(); // Limit stack size
    }

    /**
     * Begin editing a block by deep cloning its props.
     */
    function beginEditingBlock(blockId: string) {
        const block = blocks.value.find((b) => b.id === blockId);
        if (block) {
            currentBlockId.value = blockId;
            // Deep clone props to avoid mutating the original until save
            editingBlockProps.value = JSON.parse(JSON.stringify(block.props));
        } else {
            // Block not found, clear editing state
            discardBlock();
        }
    }

    /**
     * Update the temporary editingBlockProps with new partial data.
     */
    function updateEditingBlockProps(newProps: any) {
        if (!currentBlockId.value || !editingBlockProps.value) return;
        editingBlockProps.value = {
            ...editingBlockProps.value,
            ...newProps,
        };
    }

    /**
     * Save edits from editingBlockProps to the main blocks array.
     */
    function saveBlock() {
        if (!currentBlockId.value || !editingBlockProps.value) return;
        const idx = blocks.value.findIndex(
            (b) => b.id === currentBlockId.value
        );
        if (idx !== -1) {
            updateBlock(currentBlockId.value, editingBlockProps.value);
        }
        discardBlock();
    }

    /**
     * Cancel editing and clear editing state.
     */
    function discardBlock() {
        currentBlockId.value = null;
        editingBlockProps.value = null;
    }

    /**
     * Handle block image processed event from WebSocket.
     */
    function handleBlockImageProcessed(data: {
        blockId: string;
        imageUrl: string;
        propName: string;
    }) {
        const block = blocks.value.find((b) => b.id === data.blockId);
        if (block) {
            // Clean up any blob URLs to prevent memory leaks
            if (
                block.props[data.propName] &&
                block.props[data.propName].startsWith("blob:")
            ) {
                URL.revokeObjectURL(block.props[data.propName]);
            }

            // Update with the final URL (the backend will convert path to full URL)
            block.props[data.propName] = data.imageUrl;

            // Remove the specific uploading flag
            delete block.props[`_${data.propName}_uploadingToS3`];

            console.log(
                `Block ${data.blockId} image processed: ${data.propName} = ${data.imageUrl}`
            );

            // If this block is currently being edited, update the editing props too
            if (
                currentBlockId.value === data.blockId &&
                editingBlockProps.value
            ) {
                editingBlockProps.value[data.propName] = data.imageUrl;
                delete editingBlockProps.value[
                    `_${data.propName}_uploadingToS3`
                ];
            }
        }
    }

    // --- Expose State & Actions ---
    return {
        // State
        blocks,
        websiteId,
        websiteSlug,
        isPublished,
        saveState,
        isDirty,
        saveError,
        currentEvent,
        previewDevice,
        undoStack,
        redoStack,
        currentBlockId,
        editingBlockProps,
        lastUpdatedAt,
        currentBlockType,
        autoSaveInterval,
        showSavedMessage,

        // Actions
        initializeBuilder,
        addBlock,
        deleteBlock,
        updateBlock,
        saveWebsite,
        publishWebsite: publishWebsiteAction,
        unpublishWebsite: unpublishWebsiteAction,
        setPreviewDevice,
        undo,
        redo,
        beginEditingBlock,
        updateEditingBlockProps,
        saveBlock,
        discardBlock,
        handleBlockImageProcessed,
        startAutoSave,
        stopAutoSave,
    };
});
