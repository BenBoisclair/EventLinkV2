<script setup lang="ts">
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import Section from "@/Components/UI/Section.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { storeToRefs } from "pinia";
import { ref } from "vue";

const websiteBuilderStore = useWebsiteBuilderStore();
const { theme } = storeToRefs(websiteBuilderStore);

const isOpen = ref(true);

const updateTheme = (value: boolean) => {
    isOpen.value = value;
};

const handleThemeColorChange = (
    colorType: "primary" | "secondary" | "accent" | "background",
    newColor: string
) => {
    websiteBuilderStore.updateTheme({ [colorType]: newColor });
};
</script>

<template>
    <div>
        <button
            @click="updateTheme(!isOpen)"
            class="flex items-center justify-between w-full px-6 py-4 text-sm font-medium transition-colors duration-150 ease-in-out dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border': !isOpen,
            }"
        >
            <span class="text-2xl font-bold dark:text-dark-primary text-primary"
                >Theme</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ isOpen ? "$chevronUp" : "$chevronDown" }}
            </v-icon>
        </button>
        <div class="px-6">
            <div v-if="isOpen" class="pt-4 pb-6">
                <span class="text-lg font-medium">Custom</span>
                <div class="flex items-start gap-1 mt-2">
                    <ColorPalettePicker
                        :model-value="theme.primary"
                        @update:model-value="
                            (color) => handleThemeColorChange('primary', color)
                        "
                        id="primary-color"
                    />
                    <ColorPalettePicker
                        :model-value="theme.secondary"
                        @update:model-value="
                            (color) =>
                                handleThemeColorChange('secondary', color)
                        "
                        id="secondary-color"
                    />

                    <ColorPalettePicker
                        :model-value="theme.accent"
                        @update:model-value="
                            (color) => handleThemeColorChange('accent', color)
                        "
                        id="accent-color"
                    />
                    <ColorPalettePicker
                        :model-value="theme.background"
                        @update:model-value="
                            (color) =>
                                handleThemeColorChange('background', color)
                        "
                        id="background-color"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
