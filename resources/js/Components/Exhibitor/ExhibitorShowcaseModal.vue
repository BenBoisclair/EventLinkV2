<script setup lang="ts">
import Modal from '@/Components/UI/Modal.vue';
import type { Exhibitor } from '@/types/exhibitor';
import { isColorLight } from '@/utils/color';
import { resolveStorageUrl } from '@/utils/storage';
import { computed } from 'vue';

interface Props {
    modelValue: boolean;
    exhibitor: Exhibitor | null;
    backgroundColor?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const closeModal = () => {
    emit('update:modelValue', false);
};

const textColorClass = computed(() => {
    if (!props.backgroundColor) {
        return 'text-gray-900';
    }
    return isColorLight(props.backgroundColor) ? 'text-gray-900' : 'text-white';
});
</script>

<template>
    <Modal
        :model-value="modelValue"
        :title="''"
        close-on-click-outside
        :content-background-color="props.backgroundColor"
        :title-color="textColorClass"
        @update:model-value="closeModal"
    >
        <div v-if="exhibitor" class="flex flex-col gap-6 p-4">
            <img
                v-if="exhibitor.banner_path"
                :src="resolveStorageUrl(exhibitor.banner_path)"
                :alt="`${exhibitor.name} banner`"
                class="max-h-[700px] rounded-md object-contain"
                loading="lazy"
            />
            <!-- <div class="flex items-start gap-4">
                <img
                    v-if="exhibitor.logo_path"
                    :src="resolveStorageUrl(exhibitor.logo_path)"
                    :alt="`${exhibitor.name} logo`"
                    class="object-contain w-20 h-20 rounded-md shrink-0"
                    loading="lazy"
                />
                <div
                    v-else
                    class="flex items-center justify-center w-20 h-20 text-gray-400 rounded-md shrink-0 bg-gray-500/50"
                    aria-hidden="true"
                >
                    <v-icon icon="$image" size="large"></v-icon>
                </div>
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold" :class="textColorClass">
                        {{ exhibitor.name }}
                    </h1>
                    <p
                        v-if="exhibitor.description"
                        class="text-sm"
                        :class="secondaryTextColorClass"
                    >
                        {{ exhibitor.description }}
                    </p>
                </div>
            </div> -->
        </div>
    </Modal>
</template>
