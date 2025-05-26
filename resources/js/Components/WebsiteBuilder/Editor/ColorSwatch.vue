<script setup lang="ts">
import { computed, defineProps, ref, watch } from 'vue';

interface Props {
    color?: string;
    isPicker?: boolean;
    colorType?: 'primary' | 'secondary' | 'light' | 'dark';
}

const props = withDefaults(defineProps<Props>(), {
    color: undefined,
    isPicker: false,
    colorType: undefined,
});

const emit = defineEmits<{
    (e: 'update:color', value: string): void;
}>();

const isHovered = ref(false);
const pickerColor = ref(props.color);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => props.color,
    (newColor) => {
        if (newColor !== pickerColor.value) {
            pickerColor.value = newColor;
        }
    },
);

const debounce = (func: (...args: any[]) => void, delay: number) => {
    return (...args: any[]) => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(() => {
            func(...args);
        }, delay);
    };
};

const debouncedEmitUpdate = debounce((newColor: string) => {
    emit('update:color', newColor);
}, 300);

const handleColorUpdate = (newColor: string) => {
    debouncedEmitUpdate(newColor);
};

const handleMouseEnter = () => {
    isHovered.value = true;
};

const handleMouseLeave = () => {
    isHovered.value = false;
};

const tooltipProps = computed(() => ({
    text: props.colorType
        ? props.colorType.charAt(0).toUpperCase() + props.colorType.slice(1)
        : '',
    location: 'bottom' as const,
    offset: 5,
}));

const iconSrc = computed(() => {
    if (!props.color || (props.isPicker && isHovered.value)) {
        return props.color && isHovered.value
            ? '/icons/pipette-white.svg'
            : '/icons/pipette.svg';
    }
    return null;
});
</script>

<template>
    <div class="relative">
        <v-menu
            :disabled="!isPicker"
            :close-on-content-click="false"
            location="bottom"
            :offset="10"
        >
            <template v-slot:activator="{ props: menuProps }">
                <button
                    v-bind="isPicker ? menuProps : {}"
                    v-tooltip="tooltipProps"
                    type="button"
                    :class="[
                        'flex h-8 w-10 items-center justify-center rounded-full border border-border',
                        color ? 'relative' : '',
                    ]"
                    :style="color ? { backgroundColor: color } : {}"
                    @mouseenter="handleMouseEnter"
                    @mouseleave="handleMouseLeave"
                >
                    <img v-if="iconSrc" :src="iconSrc" alt="Pick color" />
                </button>
            </template>

            <v-color-picker
                v-if="isPicker"
                :model-value="pickerColor"
                @update:model-value="handleColorUpdate"
                elevation="0"
                mode="hex"
                bg-color="white"
                :modes="['hex']"
            />
        </v-menu>
    </div>
</template>
