<script setup lang="ts">
import Select from "@/Components/Forms/Select.vue";
import Section from "@/Components/UI/Section.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { Styling } from "@/types/websiteBuilder";
import { storeToRefs } from "pinia";
import { ref } from "vue";

const websiteBuilderStore = useWebsiteBuilderStore();
const { styling } = storeToRefs(websiteBuilderStore);

const isOpen = ref(true);

const updateStyle = (value: boolean) => {
    isOpen.value = value;
};

const handleStylingChange = (
    styleType: keyof Styling,
    newValue: string
) => {
    websiteBuilderStore.updateStyling({ [styleType]: newValue });
};

// Options for each styling property
const borderRadiusOptions = [
    { value: "none", label: "None (Square)" },
    { value: "small", label: "Small" },
    { value: "medium", label: "Medium" },
    { value: "large", label: "Large" },
    { value: "full", label: "Full (Rounded)" },
];

const buttonSizeOptions = [
    { value: "xs", label: "Extra Small" },
    { value: "sm", label: "Small" },
    { value: "md", label: "Medium" },
    { value: "lg", label: "Large" },
    { value: "xl", label: "Extra Large" },
];

const shadowOptions = [
    { value: "none", label: "None" },
    { value: "xs", label: "Extra Small" },
    { value: "sm", label: "Small" },
    { value: "md", label: "Medium" },
    { value: "lg", label: "Large" },
    { value: "xl", label: "Extra Large" },
];

const buttonStyleOptions = [
    { value: "solid", label: "Solid" },
    { value: "outline", label: "Outline" },
    { value: "ghost", label: "Ghost" },
];

const animationSpeedOptions = [
    { value: "none", label: "None" },
    { value: "slow", label: "Slow" },
    { value: "normal", label: "Normal" },
    { value: "fast", label: "Fast" },
];

const fontWeightOptions = [
    { value: "light", label: "Light" },
    { value: "normal", label: "Normal" },
    { value: "medium", label: "Medium" },
    { value: "semibold", label: "Semi Bold" },
    { value: "bold", label: "Bold" },
];

const letterSpacingOptions = [
    { value: "tight", label: "Tight" },
    { value: "normal", label: "Normal" },
    { value: "wide", label: "Wide" },
];
</script>

<template>
    <div>
        <button
            @click="updateStyle(!isOpen)"
            class="flex items-center justify-between w-full px-6 py-4 text-sm font-medium transition-colors duration-150 ease-in-out dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border': !isOpen,
            }"
        >
            <span class="text-2xl font-bold dark:text-dark-primary text-primary"
                >Styling</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ isOpen ? "$chevronUp" : "$chevronDown" }}
            </v-icon>
        </button>
        <div class="px-6 py-4 pb-6">
            <div v-if="isOpen" class="space-y-4">
                
                <!-- Border Radius -->
                <div>
                    <Select
                        :model-value="styling.borderRadius"
                        @update:model-value="(value) => handleStylingChange('borderRadius', value as string)"
                        :options="borderRadiusOptions"
                        label="Border Radius"
                        id="border-radius-select"
                    />
                </div>

                <!-- Button Size -->
                <div>
                    <Select
                        :model-value="styling.buttonSize"
                        @update:model-value="(value) => handleStylingChange('buttonSize', value as string)"
                        :options="buttonSizeOptions"
                        label="Button Size"
                        id="button-size-select"
                    />
                </div>

                <!-- Shadow -->
                <div>
                    <Select
                        :model-value="styling.shadow"
                        @update:model-value="(value) => handleStylingChange('shadow', value as string)"
                        :options="shadowOptions"
                        label="Shadow"
                        id="shadow-select"
                    />
                </div>

                <!-- Button Style -->
                <div>
                    <Select
                        :model-value="styling.buttonStyle"
                        @update:model-value="(value) => handleStylingChange('buttonStyle', value as string)"
                        :options="buttonStyleOptions"
                        label="Button Style"
                        id="button-style-select"
                    />
                </div>

                <!-- Animation Speed -->
                <div>
                    <Select
                        :model-value="styling.animationSpeed"
                        @update:model-value="(value) => handleStylingChange('animationSpeed', value as string)"
                        :options="animationSpeedOptions"
                        label="Animation Speed"
                        id="animation-speed-select"
                    />
                </div>

                <!-- Font Weight -->
                <div>
                    <Select
                        :model-value="styling.fontWeight"
                        @update:model-value="(value) => handleStylingChange('fontWeight', value as string)"
                        :options="fontWeightOptions"
                        label="Font Weight"
                        id="font-weight-select"
                    />
                </div>

                <!-- Letter Spacing -->
                <div>
                    <Select
                        :model-value="styling.letterSpacing"
                        @update:model-value="(value) => handleStylingChange('letterSpacing', value as string)"
                        :options="letterSpacingOptions"
                        label="Letter Spacing"
                        id="letter-spacing-select"
                    />
                </div>

            </div>
        </div>
    </div>
</template>