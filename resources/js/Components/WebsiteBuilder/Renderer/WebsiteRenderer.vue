<script setup lang="ts">
import { getBlockComponent } from "@/utils/blockRegistry";
import { buildBlockProps } from "@/utils/blockPropsBuilder";
import { extractWebsiteId } from "@/utils/websiteHelpers";
import type { Block } from "@/types/websiteBuilder";
import { usePage } from "@inertiajs/vue3";
import { computed, StyleValue } from "vue";

interface Props {
    blocks: Block[];
    selectedFont?: string;
    isEditorMode?: boolean;
    device?: "mobile" | "tablet" | "desktop";
    event?: {
        id: number;
        name?: string;
        start_date?: string;
        end_date?: string;
        location?: string;
    };
    editingBlockId?: string | null;
    editingBlockProps?: Record<string, any> | null;
}

const props = withDefaults(defineProps<Props>(), {
    eventName: "Event Website",
    blocks: () => [],
    selectedFont: "Inter",
    isEditorMode: false,
    event: undefined,
    editingBlockId: null,
    editingBlockProps: null,
});

const effectiveDevice = computed(() => props.device || "desktop");

const emit = defineEmits<{
    (e: "deleteBlock", blockId: string): void;
    (e: "updateBlock", blockId: string, newProps: Record<string, any>): void;
}>();

const prepareBlockProps = (block: Block) => {
    return buildBlockProps(
        block,
        props.editingBlockId,
        props.editingBlockProps,
        props.isEditorMode || false,
        effectiveDevice.value,
        props.event,
        extractWebsiteId(usePage().props)
    );
};

const rendererStyle = computed((): StyleValue => {
    return {
        fontFamily: props.selectedFont
            ? `"${props.selectedFont}", sans-serif`
            : "sans-serif",
    };
});

console.log(props.blocks);
</script>

<template>
    <div class="flex flex-col min-h-full bg-white" :style="rendererStyle">
        <div class="flex-1">
            <div
                v-if="!blocks || blocks.length === 0"
                class="flex min-h-[500px] flex-1 items-center justify-center p-6"
            >
                <div class="text-center text-gray-500">
                    <p class="mb-4 text-sm font-medium text-primary">
                        Nothing here yet. Add content using the editor.
                    </p>
                </div>
            </div>
            <div v-else class="w-full">
                <template v-for="block in blocks" :key="block.id">
                    <component
                        :is="getBlockComponent(block.type)"
                        v-bind="prepareBlockProps(block)"
                        @delete="$emit('deleteBlock', $event)"
                        @updateBlock="
                            (blockId, props) =>
                                $emit('updateBlock', blockId, props)
                        "
                    />
                </template>
            </div>
        </div>
    </div>
</template>
