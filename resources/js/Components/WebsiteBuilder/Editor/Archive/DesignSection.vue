<script setup lang="ts">
import InputLabel from '@/Components/Forms/InputLabel.vue';
import { useWebsiteBuilderStore } from '@/stores/websiteBuilderStore';
import { storeToRefs } from 'pinia';

const websiteBuilderStore = useWebsiteBuilderStore();
const { designSection } = storeToRefs(websiteBuilderStore);

const updateIsOpen = (value: boolean) => {
    designSection.value.isOpen = value;
};

const selectFont = (fontName: string) => {
    designSection.value.selectedFont = fontName;
};
</script>

<template>
    <div>
        <button
            @click="updateIsOpen(!designSection.isOpen)"
            class="flex items-center justify-between w-full px-6 py-6 text-sm font-medium transition-colors duration-150 ease-in-out dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border':
                    !designSection.isOpen,
            }"
        >
            <span class="text-2xl font-bold dark:text-dark-primary text-primary"
                >Design</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ designSection.isOpen ? '$chevronUp' : '$chevronDown' }}
            </v-icon>
        </button>
        <div
            v-if="designSection.isOpen"
            class="dark:border-dark-border space-y-3 border-b-[1px] border-border px-6 py-4 pb-6"
        >
            <InputLabel
                value="Font Selection"
                class="dark:text-dark-text-primary"
            />
            <div class="space-y-2">
                <div
                    v-for="font in designSection.availableFonts"
                    :key="font"
                    class="dark:border-dark-border dark:bg-dark-surface dark:text-dark-text-primary dark:hover:shadow-dark-primary/10 bg-surface cursor-pointer rounded-md border-[1px] border-border px-4 py-2 text-sm transition-shadow hover:shadow-sm dark:hover:shadow-md"
                    :class="{
                        'dark:ring-dark-primary ring-2 ring-[#193CB8]':
                            designSection.selectedFont === font,
                    }"
                    @click="selectFont(font)"
                    :style="{ fontFamily: font }"
                >
                    {{ font }}
                </div>
            </div>
            <!-- Placeholder for future design options -->
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles if needed */
</style>
