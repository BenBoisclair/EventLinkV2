<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    title?: string;
    titleColor?: string;
    tag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    textAlign?: 'left' | 'center' | 'right';
    defaultClasses?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    titleColor: '#000000',
    tag: 'h2',
    textAlign: 'center',
    defaultClasses: 'text-3xl font-bold tracking-tight sm:text-4xl',
});

const titleStyle = computed(() => ({
    color: props.titleColor || 'inherit',
    textAlign: props.textAlign,
}));

const titleClasses = computed(() => {
    return [
        props.defaultClasses,
        {
            'text-left': props.textAlign === 'left',
            'text-center': props.textAlign === 'center',
            'text-right': props.textAlign === 'right',
        },
    ];
});
</script>

<template>
    <component
        :is="props.tag"
        v-if="props.title"
        :style="titleStyle"
        :class="titleClasses"
    >
        {{ props.title }}
    </component>
</template>
