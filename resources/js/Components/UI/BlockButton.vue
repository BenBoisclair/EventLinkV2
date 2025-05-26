<template>
    <a
        v-if="href"
        :href="href"
        :class="buttonClasses"
        :style="buttonStyle"
        :target="
            href.startsWith('http') || href.startsWith('/') ? '_blank' : '_self'
        "
        :rel="
            href.startsWith('http') || href.startsWith('/')
                ? 'noopener noreferrer'
                : undefined
        "
        @click="handleClick"
    >
        <v-icon
            v-if="icon"
            :icon="icon"
            size="small"
            :class="iconClasses"
        ></v-icon>
        <slot v-if="$slots.default"></slot>
        <span v-else-if="text">{{ text }}</span>
    </a>
    <button
        v-else
        :class="buttonClasses"
        :style="buttonStyle"
        :disabled="disabled"
        type="button"
    >
        <v-icon
            v-if="icon"
            :icon="icon"
            size="small"
            :class="iconClasses"
        ></v-icon>
        <slot v-if="$slots.default"></slot>
        <span v-else-if="text">{{ text }}</span>
    </button>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    text: {
        type: String,
        required: false,
    },
    icon: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) =>
            [
                'primary',
                'secondary',
                'danger',
                'success',
                'outline-primary',
                'outline-secondary',
                'outline-danger',
                'outline-success',
                'outline-toned',
            ].includes(value),
    },
    href: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: null,
    },
    textColor: {
        type: String,
        default: null,
    },
});

const slots = useSlots();

const buttonStyle = computed(() => {
    const style = {};
    if (props.color) {
        style.backgroundColor = props.color;
        style.borderColor = props.color;
    }
    if (props.textColor) {
        style.color = props.textColor;
    }
    return style;
});

const buttonClasses = computed(() => [
    'group flex items-center justify-center rounded-lg border-solid px-3 py-1.5 transition-colors duration-150 ease-in-out hover:cursor-pointer md:px-4 md:py-2',
    { 'gap-2': props.text || slots.default },
    {
        // Primary Solid
        'border-[2px] border-[#193CB8] bg-[#193CB8] text-[#FFFFFF] hover:bg-[#15329a] active:bg-[#11277b]':
            props.variant === 'primary' && !props.color,
        // Secondary Solid
        'border-[2px] border-[#8200DB] bg-[#8200DB] text-[#FFFFFF] hover:brightness-95 active:brightness-90':
            props.variant === 'secondary' && !props.color,
        // Danger Solid
        'border-[2px] border-[#FB2C36] bg-[#FB2C36] text-[#FFFFFF] hover:brightness-95 active:brightness-90':
            props.variant === 'danger' && !props.color,
        // Success Solid
        'border-[2px] border-[#00C16A] bg-[#00C16A] text-[#FFFFFF] hover:brightness-95 active:brightness-90':
            props.variant === 'success' && !props.color,
        // Primary Outline
        'border-[2px] border-[#193CB8]/50 text-[#193CB8] hover:border-[#193CB8] hover:bg-[#193CB8] hover:text-[#FFFFFF] active:border-[#15329a] active:bg-[#15329a] active:text-[#FFFFFF]':
            props.variant === 'outline-primary' && !props.textColor,
        // Secondary Outline
        'border-[2px] border-[#8200DB]/50 text-[#8200DB] hover:border-[#8200DB] hover:bg-[#8200DB] hover:text-[#FFFFFF] active:border-[#8200DB] active:bg-[#8200DB] active:text-[#FFFFFF] active:brightness-95':
            props.variant === 'outline-secondary' && !props.textColor,
        // Danger Outline
        'border-[2px] border-[#FB2C36]/50 text-[#FB2C36] hover:border-[#FB2C36] hover:bg-[#FB2C36] hover:text-[#FFFFFF] active:border-[#FB2C36] active:bg-[#FB2C36] active:text-[#FFFFFF] active:brightness-95':
            props.variant === 'outline-danger' && !props.textColor,
        // Success Outline
        'border-[2px] border-[#00C16A]/50 text-[#00C16A] hover:border-[#00C16A] hover:bg-[#00C16A] hover:text-[#FFFFFF] active:border-[#00C16A] active:bg-[#00C16A] active:text-[#FFFFFF] active:brightness-95':
            props.variant === 'outline-success' && !props.textColor,
        // Toned Outline
        'border-[2px] border-[#45556C]/50 text-[#45556C] hover:border-[#45556C] hover:bg-[#45556C] hover:text-[#FFFFFF] active:border-[#45556C] active:bg-[#45556C] active:text-[#FFFFFF] active:brightness-95':
            props.variant === 'outline-toned' && !props.textColor,
        // Disabled (applied to anchor via CSS, button via attribute)
        'pointer-events-none cursor-not-allowed opacity-50':
            props.disabled && props.href,
    },
]);

const iconClasses = computed(() => {
    const classes = {};
    // Apply text color based on variant only if textColor prop is not set
    if (!props.textColor) {
        if (props.variant === 'outline-primary') {
            classes['text-[#193CB8]'] = true;
            classes['group-hover:text-[#FFFFFF]'] = true; // Ensure icon color changes on hover for outline
        } else if (props.variant === 'outline-secondary') {
            classes['text-[#8200DB]'] = true;
            classes['group-hover:text-[#FFFFFF]'] = true;
        } else if (props.variant === 'outline-danger') {
            classes['text-[#FB2C36]'] = true;
            classes['group-hover:text-[#FFFFFF]'] = true;
        } else if (props.variant === 'outline-success') {
            classes['text-[#00C16A]'] = true;
            classes['group-hover:text-[#FFFFFF]'] = true;
        } else if (props.variant === 'outline-toned') {
            classes['text-[#45556C]'] = true;
            classes['group-hover:text-[#FFFFFF]'] = true;
        }
    }
    return classes;
});

// Prevent default if anchor is disabled
const handleClick = (event) => {
    if (props.disabled && props.href) {
        event.preventDefault();
    }
};
</script>
