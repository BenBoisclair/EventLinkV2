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
    event?: {
        id: number;
        name?: string;
        start_date?: string;
        end_date?: string;
        location?: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    blocks: () => [],
    selectedFont: "Inter",
    event: undefined,
});


const prepareBlockProps = (block: Block) => {
    return buildBlockProps(
        block,
        null, // No editing in display mode
        null, // No editing props
        false, // Not in editor mode
        "desktop", // Always render for desktop in public view
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
                    />
                </template>
            </div>
        </div>
    </div>
</template>
