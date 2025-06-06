<script setup lang="ts">
import { blockDefaults } from "@/Components/WebsiteBuilder/Blocks/blockDefaults";
import type { BlockOption } from "@/types/websiteBuilder";
import type { Block } from "@/types/websiteBuilder.ts";
import {
    BuildingStorefrontIcon,
    ChartBarIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    DocumentTextIcon,
    PaintBrushIcon,
    PhotoIcon,
} from "@heroicons/vue/24/outline";
import { computed } from "vue";

const icons = {
    ChartBarIcon,
    PhotoIcon,
    ClipboardDocumentListIcon,
    DocumentTextIcon,
    ClockIcon,
    BuildingStorefrontIcon,
    PaintBrushIcon,
};

const emit = defineEmits<{
    (e: "select", blockType: Block["type"], defaultProps?: any): void;
}>();

const blockSelectionOptions = computed((): BlockOption[] => {
    const availableTypes = Object.keys(blockDefaults) as Array<
        keyof typeof blockDefaults
    >;
    return availableTypes.map((type) => ({
        type: type,
        title: blockDefaults[type].title,
        description: blockDefaults[type].description,
        icon: blockDefaults[type].icon,
    }));
});

const handleSelect = (blockType: Block["type"]) => {
    const defaultProps = blockDefaults[blockType]?.props || {};
    emit("select", blockType, defaultProps);
};
</script>

<template>
    <div class="space-y-3">
        <ul class="space-y-2">
            <li
                v-for="option in blockSelectionOptions"
                :key="option.type"
                class="flex items-center p-4 space-x-4 transition duration-150 ease-in-out border border-gray-300 rounded-lg cursor-pointer dark:border-dark-border dark:hover:border-dark-primary dark:hover:bg-dark-primary/10 hover:border-primary hover:bg-primary/5 hover:shadow-md"
                @click="handleSelect(option.type)"
            >
                <component
                    :is="icons[option.icon as keyof typeof icons]"
                    class="flex-shrink-0 w-6 h-6 dark:text-dark-primary text-primary"
                    aria-hidden="true"
                />
                <div class="flex-1">
                    <p
                        class="font-semibold dark:text-dark-primary text-primary"
                    >
                        {{ option.title }}
                    </p>
                </div>
            </li>
        </ul>
    </div>
</template>
