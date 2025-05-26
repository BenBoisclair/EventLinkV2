<script setup lang="ts">
import type { Exhibitor } from '@/types/exhibitor';
import { isColorLight } from '@/utils/color';
import { resolveStorageUrl } from '@/utils/storage';
import { computed, ref } from 'vue';
import Button from '../UI/Button.vue';

interface Props {
    exhibitor: Exhibitor;
    cardBackgroundColor?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'click', exhibitor: Exhibitor): void;
}>();

const imageError = ref(false);

const textColorClass = computed(() => {
    if (!props.cardBackgroundColor) {
        return 'text-gray-900';
    }
    return isColorLight(props.cardBackgroundColor)
        ? 'text-gray-900'
        : 'text-white';
});

const buttonTextColorClass = computed(() => {
    if (!props.cardBackgroundColor) {
        return 'text-blue-600 hover:text-blue-700';
    }
    return isColorLight(props.cardBackgroundColor)
        ? 'text-blue-600 hover:text-blue-700'
        : 'text-blue-300 hover:text-blue-200';
});

const buttonBorderColorClass = computed(() => {
    if (!props.cardBackgroundColor) {
        return 'border-blue-600 hover:border-blue-700';
    }
    return isColorLight(props.cardBackgroundColor)
        ? 'border-blue-600 hover:border-blue-700'
        : 'border-blue-300 hover:border-blue-200';
});
</script>

<template>
    <div class="flex flex-col items-center w-full gap-4 p-2 rounded-lg">
        <img
            v-if="!imageError"
            :src="resolveStorageUrl(exhibitor.logo_path)"
            :alt="`${exhibitor.name} logo`"
            class="object-cover w-full rounded-lg cursor-pointer aspect-video shrink-0"
            loading="lazy"
            @error="imageError = true"
            @click="emit('click', exhibitor)"
        />
        <div
            v-else
            class="w-full bg-gray-800 rounded-md rounded-lg aspect-square shrink-0"
            aria-hidden="true"
        ></div>

        <div class="flex-1 w-full">
            <h3
                class="text-lg font-bold tracking-tight"
                :class="textColorClass"
            >
                {{ exhibitor.name }}
            </h3>
            <p
                v-if="exhibitor.description"
                class="mt-2 text-sm cursor-pointer line-clamp-3"
                :class="
                    textColorClass === 'text-white'
                        ? 'text-gray-200'
                        : 'text-text-muted'
                "
                @click="emit('click', exhibitor)"
            >
                {{ exhibitor.description }}
            </p>
        </div>

        <div class="flex items-center justify-between w-full mt-4">
            <span class="text-sm">{{ exhibitor.contact_email }}</span>
            <Button
                class="flex items-center gap-1 text-sm"
                :class="buttonTextColorClass"
                variant="outline-primary"
                :border-color="buttonBorderColorClass"
                @click="emit('click', exhibitor)"
                >Read Now! <v-icon icon="$arrowTopRight" size="small"
            /></Button>
        </div>
    </div>
</template>
