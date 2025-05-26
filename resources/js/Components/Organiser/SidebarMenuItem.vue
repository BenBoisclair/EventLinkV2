<script setup lang="ts">
import { computed, ref } from 'vue';

const props = defineProps<{
    name: string;
    icon: string;
    isActive?: boolean;
    isCollapsed?: boolean;
    isDark?: boolean;
}>();

const emit = defineEmits<{
    (e: 'click'): void;
}>();

const isHovered = ref(false);

const showActiveState = computed(() => {
    return props.isActive || isHovered.value;
});
</script>

<template>
    <div
        class="border-l-2 px-[6px]"
        :class="{
            'dark:border-dark-primary border-[#193CB8]': showActiveState,
            'dark:border-dark-border border-[#E2E8F0]': !showActiveState,
        }"
    >
        <button
            class="flex items-center px-[10px] py-[6px] text-left text-[14px] font-medium transition-colors"
            :class="{
                'dark:text-dark-primary text-[#193CB8]': showActiveState,
                'dark:text-dark-text-secondary text-[#62748E]':
                    !showActiveState,
                'justify-center': isCollapsed,
            }"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
            @click="emit('click')"
        >
            <v-icon
                :icon="icon"
                :color="
                    showActiveState
                        ? isDark
                            ? '#4C6EF5'
                            : '#193CB8'
                        : isDark
                          ? '#94A3B8'
                          : '#62748E'
                "
                size="small"
                :class="{
                    'mr-[6px]': !isCollapsed,
                }"
            />
            <span v-if="!isCollapsed">{{ name }}</span>
        </button>
    </div>
</template>

<style scoped></style>
