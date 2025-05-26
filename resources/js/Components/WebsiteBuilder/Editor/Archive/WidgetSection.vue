<script setup lang="ts">
import Input from '@/Components/Forms/Input.vue';
import Toggle from '@/Components/Forms/Toggle.vue';
import ColorPalettePicker from '@/Components/UI/ColorPalettePicker.vue';
import Section from '@/Components/UI/Section.vue';
import { useWebsiteBuilderStore } from '@/stores/websiteBuilderStore';
import { storeToRefs } from 'pinia';

const websiteBuilderStore = useWebsiteBuilderStore();
const { widgetsSection } = storeToRefs(websiteBuilderStore);

const updateSectionOpen = (value: boolean) => {
    widgetsSection.value.isOpen = value;
};

const toggleWidgetOpen = (widgetId: string) => {
    const itemIndex = widgetsSection.value.items.findIndex(
        (item) => item.id === widgetId,
    );
    if (itemIndex !== -1) {
        widgetsSection.value.items[itemIndex].isOpen =
            !widgetsSection.value.items[itemIndex].isOpen;
    }
};

const updateWidgetEnabled = (widgetId: string, enabled: boolean) => {
    const itemIndex = widgetsSection.value.items.findIndex(
        (item) => item.id === widgetId,
    );
    if (itemIndex !== -1) {
        widgetsSection.value.items[itemIndex].enabled = enabled;
        if (!enabled) {
            widgetsSection.value.items[itemIndex].isOpen = false;
        }
    }
};
</script>

<template>
    <div>
        <button
            @click="updateSectionOpen(!widgetsSection.isOpen)"
            class="dark:hover:bg-dark-surface-elevated dark:active:bg-dark-surface flex w-full items-center justify-between px-6 py-6 text-sm font-medium transition-colors duration-150 ease-in-out hover:bg-gray-100 active:bg-gray-200"
            :class="{
                'dark:border-dark-border border-b-[1px] border-border':
                    !widgetsSection.isOpen,
            }"
        >
            <span class="dark:text-dark-primary text-2xl font-bold text-primary"
                >Widgets</span
            >
            <v-icon class="dark:text-dark-text-secondary">
                {{ widgetsSection.isOpen ? '$chevronUp' : '$chevronDown' }}
            </v-icon>
        </button>
        <div
            v-if="widgetsSection.isOpen"
            class="dark:border-dark-border space-y-3 border-b-[1px] border-border px-6 py-4 pb-6"
        >
            <div
                v-for="widget in widgetsSection.items"
                :key="widget.id"
                class="dark:border-dark-border dark:bg-dark-surface bg-surface rounded-lg border-[1px] border-border px-6 py-4"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <Toggle
                            v-model="widget.enabled"
                            :label="widget.name"
                            @update:modelValue="
                                (value: boolean) =>
                                    updateWidgetEnabled(widget.id, value)
                            "
                        />
                    </div>
                    <button
                        @click="toggleWidgetOpen(widget.id)"
                        class="dark:text-dark-text-secondary dark:hover:text-dark-text-primary text-gray-400 hover:text-gray-600"
                    >
                        <v-icon>
                            {{ widget.isOpen ? '$chevronUp' : '$chevronDown' }}
                        </v-icon>
                    </button>
                </div>

                <!-- Header Content -->
                <div
                    v-if="widget.id === 'header' && widget.isOpen"
                    class="mt-4"
                >
                    <div class="space-y-4">
                        <!-- Button Customization -->
                        <Section class="dark:bg-dark-surface-elevated">
                            <h3
                                class="dark:text-dark-text-primary mb-3 text-lg font-semibold"
                            >
                                Button
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label
                                        for="buttonText"
                                        class="dark:text-dark-text-secondary mb-1 block text-sm font-medium"
                                        >Button Text</label
                                    >
                                    <Input
                                        id="buttonText"
                                        v-model="widget.buttonText"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="buttonLink"
                                        class="dark:text-dark-text-secondary mb-1 block text-sm font-medium"
                                        >Button Link</label
                                    >
                                    <Input
                                        type="url"
                                        id="buttonLink"
                                        v-model="widget.buttonLink"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="buttonTextColor"
                                        class="dark:text-dark-text-secondary mb-1 block text-sm font-medium"
                                        >Button Text Color</label
                                    >
                                    <ColorPalettePicker
                                        id="buttonTextColor"
                                        v-model="widget.buttonTextColor"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="buttonBackgroundColor"
                                        class="dark:text-dark-text-secondary mb-1 block text-sm font-medium"
                                        >Button Background Color</label
                                    >
                                    <ColorPalettePicker
                                        id="buttonBackgroundColor"
                                        v-model="widget.buttonBackgroundColor"
                                    />
                                </div>
                            </div>
                        </Section>
                    </div>
                </div>

                <!-- Footer Content -->
                <div
                    v-if="widget.id === 'footer' && widget.isOpen"
                    class="mt-4"
                ></div>
            </div>
        </div>
    </div>
</template>
