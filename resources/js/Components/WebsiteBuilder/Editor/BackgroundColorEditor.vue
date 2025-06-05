<script setup lang="ts">
import Section from "@/Components/UI/Section.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import { computed } from "vue";

interface Props {
    useThemeBackground?: boolean;
    backgroundColor?: string;
}

interface Emits {
    (e: "update:useThemeBackground", value: boolean): void;
    (e: "update:backgroundColor", value: string): void;
}

const props = withDefaults(defineProps<Props>(), {
    useThemeBackground: true,
    backgroundColor: undefined,
});

const emit = defineEmits<Emits>();

// Computed properties for v-model binding
const internalUseThemeBackground = computed({
    get: () => props.useThemeBackground ?? true,
    set: (value: boolean) => emit("update:useThemeBackground", value),
});

const internalBackgroundColor = computed({
    get: () => props.backgroundColor ?? "#ffffff",
    set: (value: string) => emit("update:backgroundColor", value),
});
</script>

<template>
    <Section title="Background">
        <div class="space-y-4">
            <!-- Theme Background Toggle -->
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-gray-700 dark:text-dark-text-secondary">
                    Use Theme Background
                </label>
                <Toggle v-model="internalUseThemeBackground" />
            </div>

            <!-- Custom Background Color -->
            <div v-show="!internalUseThemeBackground" class="transition-all duration-200">
                <ColorPalettePicker
                    v-model="internalBackgroundColor"
                    label="Background Color"
                    id="blockBackgroundColor"
                />
            </div>
        </div>
    </Section>
</template>