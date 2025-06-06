<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { ref, computed } from "vue";
import draggable from "vuedraggable";

const websiteBuilderStore = useWebsiteBuilderStore();
const isOpen = ref(false);
const isDragging = ref(false);

// Create a computed property for v-model binding with draggable
const blocks = computed({
    get: () => websiteBuilderStore.blocks,
    set: (value) => {
        websiteBuilderStore.blocks = value;
    },
});

const updateBlocks = (value: boolean) => {
    isOpen.value = value;
};

const onDragStart = () => {
    isDragging.value = true;
};

const onDragEnd = () => {
    isDragging.value = false;
    // The blocks array is automatically updated by vuedraggable
};
</script>

<template>
    <div>
        <button
            @click="updateBlocks(!isOpen)"
            class="flex items-center justify-between w-full px-6 py-4 text-sm font-medium transition-colors duration-150 ease-in-out dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border': !isOpen,
            }"
        >
            <span class="text-2xl font-bold dark:text-dark-primary text-primary"
                >Blocks</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ isOpen ? "$chevronUp" : "$chevronDown" }}
            </v-icon>
        </button>
        <div class="px-6">
            <div v-if="isOpen" class="pt-4 pb-6">
                <!-- Empty state when no blocks -->
                <div
                    v-if="!blocks || blocks.length === 0"
                    class="p-8 mb-6 text-center border-2 border-gray-300 border-dashed rounded-lg dark:border-dark-border"
                >
                    <p class="mb-2 text-gray-500 dark:text-dark-text-secondary">
                        No blocks added yet
                    </p>
                    <p
                        class="text-sm text-gray-400 dark:text-dark-text-tertiary"
                    >
                        Click "Add Block" to start building your website
                    </p>
                </div>

                <!-- Blocks list -->
                <div
                    v-else
                    class="relative p-2 mb-6 -m-2 transition-all duration-200 dark:border-dark-border border-border"
                    :class="{
                        'dragging-container': isDragging,
                    }"
                >
                    <draggable
                        v-model="blocks"
                        :animation="200"
                        handle=".drag-handle"
                        ghost-class="sortable-ghost"
                        chosen-class="sortable-chosen"
                        drag-class="sortable-drag"
                        @start="onDragStart"
                        @end="onDragEnd"
                        item-key="id"
                        class="space-y-4"
                    >
                        <template #item="{ element: block, index }">
                            <Section
                                :key="block.id"
                                class="transition-all duration-200 dark:bg-dark-surface block-item"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="p-1 -m-1 transition-colors rounded cursor-move drag-handle hover:bg-gray-100 dark:hover:bg-dark-surface-elevated"
                                        >
                                            <img
                                                src="/icons/drag.svg"
                                                alt="Drag to reorder"
                                                class="w-5 h-5 opacity-60 dark:opacity-40"
                                            />
                                        </div>
                                        <span
                                            class="font-medium dark:text-dark-text-primary"
                                            >{{ block.type }}</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="
                                                websiteBuilderStore.deleteBlock(
                                                    block.id
                                                )
                                            "
                                            class="text-gray-400 transition-colors dark:text-dark-text-secondary dark:hover:text-red-500 hover:text-red-500"
                                        >
                                            <v-icon class="w-2 h-2"
                                                >$trashCanOutline</v-icon
                                            >
                                        </button>
                                    </div>
                                </div>
                            </Section>
                        </template>
                    </draggable>
                </div>
                <Button
                    text="Add Block"
                    icon="$plus"
                    variant="primary"
                    class="w-full"
                    @click="$emit('request-add-block')"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Dotted border around container when dragging */
.dragging-container {
    border: 2px dashed rgb(83, 44, 203, 0.4) !important;
    border-radius: 8px;
    background: rgba(34, 157, 228, 0.05);
}

.dark .dragging-container {
    border-color: #888 !important;
    background: rgba(255, 255, 255, 0.02);
}

/* Ghost element (placeholder) */
.sortable-ghost {
    opacity: 0.3;
    background: #f0f0f0 !important;
    border: 2px dashed #999 !important;
    border-radius: 8px;
}

.dark .sortable-ghost {
    background: #333 !important;
    border-color: #666 !important;
}

/* Chosen element (being dragged) */
.sortable-chosen {
    opacity: 0.9;
    transform: scale(1.02);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    cursor: grabbing !important;
}

.dark .sortable-chosen {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
}

/* Smooth transitions for non-dragging blocks */
.block-item:not(.sortable-chosen) {
    transition: transform 0.2s ease;
}

/* Drag handle styling */
.drag-handle {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Prevent text selection while dragging */
.dragging-container * {
    user-select: none !important;
}
</style>
