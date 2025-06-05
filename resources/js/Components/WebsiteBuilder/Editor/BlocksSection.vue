<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { ref } from "vue";

const websiteBuilderStore = useWebsiteBuilderStore();
const isOpen = ref(false);

const updateBlocks = (value: boolean) => {
    isOpen.value = value;
};

const moveBlockUp = (index: number) => {
    if (index === 0) return;
    const items = websiteBuilderStore.blocks;
    const itemToMove = items[index];
    items.splice(index, 1);
    items.splice(index - 1, 0, itemToMove);
};

const moveBlockDown = (index: number) => {
    if (index === websiteBuilderStore.blocks.length - 1) return;
    const items = websiteBuilderStore.blocks;
    const itemToMove = items[index];
    items.splice(index, 1);
    items.splice(index + 1, 0, itemToMove);
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
        <div
            v-if="isOpen"
            class="dark:border-dark-border relative space-y-4 border-b-[1px] border-border px-6 py-4 pb-6"
        >
            <div class="space-y-4">
                <Section
                    v-for="(block, index) in websiteBuilderStore.blocks"
                    :key="block.id"
                    class="dark:bg-dark-surface"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="font-medium dark:text-dark-text-primary"
                                >{{ block.type }}</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="moveBlockUp(index)"
                                :disabled="index === 0"
                                class="text-gray-400 dark:text-dark-text-secondary dark:hover:text-dark-text-primary hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <v-icon>$chevronUp</v-icon>
                            </button>
                            <button
                                @click="moveBlockDown(index)"
                                :disabled="
                                    index ===
                                    websiteBuilderStore.blocks.length - 1
                                "
                                class="text-gray-400 dark:text-dark-text-secondary dark:hover:text-dark-text-primary hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <v-icon>$chevronDown</v-icon>
                            </button>
                        </div>
                    </div>
                </Section>
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
</template>
