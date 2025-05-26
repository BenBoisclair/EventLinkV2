<script setup lang="ts">
import { useWebsiteBuilderStore } from '@/stores/websiteBuilderStore';
import type { ThemeOptions, ThemeSection } from '@/types/websiteBuilder';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import ColorSwatch from './ColorSwatch.vue';

const websiteBuilderStore = useWebsiteBuilderStore();
const { themeSection } = storeToRefs(websiteBuilderStore);

const updateIsOpen = (value: boolean) => {
    themeSection.value.isOpen = value;
};

const selectTheme = (themeName: ThemeOptions) => {
    if (themeName !== 'custom' || themeSection.value.themes.custom) {
        themeSection.value.selectedTheme = themeName;
    }
};

const updateCustomThemeColor = (
    colorType: keyof ThemeSection['themes']['custom'],
    color: string,
) => {
    if (!themeSection.value.themes.custom) return;

    themeSection.value.selectedTheme = 'custom';
    themeSection.value.themes.custom[colorType] = color;
};

const predefinedThemeNames = computed(() => {
    return (Object.keys(themeSection.value.themes) as ThemeOptions[]).filter(
        (name) => name !== 'custom',
    );
});

type ColorType = keyof ThemeSection['themes'][ThemeOptions];
const colorTypes: ColorType[] = ['primary', 'secondary', 'light', 'dark'];
</script>

<template>
    <div>
        <button
            @click="updateIsOpen(!themeSection.isOpen)"
            class="flex items-center justify-between w-full px-6 py-6 text-sm font-medium transition-colors duration-150 ease-in- dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border':
                    !themeSection.isOpen,
            }"
        >
            <span class="text-2xl font-bold dark:text-dark-primary text-primary"
                >Color Theme</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ themeSection.isOpen ? '$chevronUp' : '$chevronDown' }}
            </v-icon>
        </button>
        <div
            v-if="themeSection.isOpen"
            class="dark:border-dark-border border-b-[1px] border-border px-6 py-4 pb-6"
        >
            <div class="space-y-4">
                <div
                    class="py-2 pl-2 pr-2 space-y-4 overflow-visible overflow-y-auto -pl-1 scroll-fade-vertical max-h-72"
                >
                    <div
                        v-for="themeName in predefinedThemeNames"
                        :key="themeName"
                        class="dark:border-dark-border dark:bg-dark-surface dark:hover:shadow-dark-primary/10 bg-surface mb-3 cursor-pointer rounded-lg border-[1px] border-border px-6 py-4 transition-shadow hover:shadow-md dark:hover:shadow-lg"
                        :class="{
                            'dark:ring-dark-primary ring-2 ring-[#193CB8]':
                                themeSection.selectedTheme === themeName,
                        }"
                        @click="selectTheme(themeName)"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="font-medium capitalize dark:text-dark-text-primary"
                                >{{ themeName }}</span
                            >
                            <div class="flex gap-2">
                                <ColorSwatch
                                    v-for="type in colorTypes"
                                    :key="type"
                                    :color="
                                        themeSection.themes[themeName]?.[type]
                                    "
                                    :colorType="type"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <hr
                    v-if="predefinedThemeNames.length > 0"
                    class="my-4 border-t dark:border-dark-border border-border"
                />

                <div
                    v-if="themeSection.themes.custom"
                    class="dark:border-dark-border dark:bg-dark-surface dark:hover:shadow-dark-primary/10 bg-surface rounded-lg border-[1px] border-border px-6 py-4 transition-shadow hover:shadow-md dark:hover:shadow-lg"
                    :class="{
                        'cursor-pointer':
                            themeSection.selectedTheme !== 'custom',
                        'dark:ring-dark-primary ring-2 ring-[#193CB8]':
                            themeSection.selectedTheme === 'custom',
                    }"
                    @click="selectTheme('custom')"
                >
                    <div class="flex items-center justify-between">
                        <span class="font-medium dark:text-dark-text-primary"
                            >Custom</span
                        >
                        <div class="flex gap-2">
                            <ColorSwatch
                                v-for="type in colorTypes"
                                :key="type"
                                :color="themeSection.themes.custom[type]"
                                :is-picker="true"
                                :colorType="type"
                                @update:color="
                                    (color: string) =>
                                        updateCustomThemeColor(type, color)
                                "
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
