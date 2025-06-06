<template>
    <button
        :class="[
            'group flex items-center justify-center rounded-lg border-solid px-3 py-1.5 transition-colors duration-150 ease-in-out hover:cursor-pointer md:px-4 md:py-2',
            { 'gap-2': text || $slots.default },
            {
                'border-none bg-primary text-white hover:bg-primary active:bg-[#11277b]':
                    variant === 'primary' && !color,
                'dark:border-dark-primary dark:bg-dark-primary dark:text-dark-text-primary dark:hover:bg-dark-primary-dimmed dark:active:bg-[#2a41a5]':
                    variant === 'primary' && !color,
                'border-none bg-brand-secondary text-white hover:brightness-95 active:brightness-90':
                    variant === 'secondary' && !color,
                'dark:border-[#9a34f0] dark:bg-[#9a34f0] dark:hover:brightness-110 dark:active:brightness-125':
                    variant === 'secondary' && !color,
                'border-none bg-danger text-white hover:brightness-95 active:brightness-90':
                    variant === 'danger' && !color,
                'dark:border-dark-status-red dark:bg-dark-status-red dark:hover:brightness-110 dark:active:brightness-125':
                    variant === 'danger' && !color,
                'border-none bg-success text-white hover:brightness-95 active:brightness-90':
                    variant === 'success' && !color,
                'dark:border-dark-status-green dark:bg-dark-status-green dark:hover:brightness-110 dark:active:brightness-125':
                    variant === 'success' && !color,
                'border-[2px] border-primary/50 text-primary hover:border-primary hover:bg-primary hover:text-white active:border-[#15329a] active:bg-[#15329a] active:text-white':
                    variant === 'outline-primary',
                'dark:border-dark-primary/50 dark:text-dark-primary dark:hover:border-dark-primary dark:hover:bg-dark-primary dark:hover:text-dark-text-primary dark:active:border-dark-primary-dimmed dark:active:bg-dark-primary-dimmed':
                    variant === 'outline-primary',
                'border-[2px] border-brand-secondary/50 text-brand-secondary hover:border-brand-secondary hover:bg-brand-secondary hover:text-white active:border-brand-secondary active:bg-brand-secondary active:text-white active:brightness-95':
                    variant === 'outline-secondary',
                'dark:border-[#9a34f0]/50 dark:text-[#b166ff] dark:hover:border-[#9a34f0] dark:hover:bg-[#9a34f0] dark:hover:text-white dark:active:border-[#8a2bdb] dark:active:bg-[#8a2bdb]':
                    variant === 'outline-secondary',
                'border-[2px] border-danger/50 text-danger hover:border-danger hover:bg-danger hover:text-white active:border-danger active:bg-danger active:text-white active:brightness-95':
                    variant === 'outline-danger',
                'dark:border-dark-status-red/50 dark:text-dark-status-red dark:hover:border-dark-status-red dark:hover:bg-dark-status-red dark:hover:text-white dark:active:border-[#ff3b45] dark:active:bg-[#ff3b45]':
                    variant === 'outline-danger',
                'border-[2px] border-success/50 text-success hover:border-success hover:bg-success hover:text-white active:border-success active:bg-success active:text-white active:brightness-95':
                    variant === 'outline-success',
                'dark:border-dark-status-green/50 dark:text-dark-status-green dark:hover:border-dark-status-green dark:hover:bg-dark-status-green dark:hover:text-white dark:active:border-[#28a761] dark:active:bg-[#28a761]':
                    variant === 'outline-success',
                'border-[2px] border-toned/50 text-toned hover:border-toned hover:bg-toned hover:text-white active:border-toned active:bg-toned active:text-white active:brightness-95':
                    variant === 'outline-toned',
                'dark:border-dark-text-tertiary dark:text-dark-text-secondary dark:hover:border-dark-text-secondary dark:hover:bg-dark-text-secondary dark:hover:text-dark-background dark:active:border-dark-text-primary dark:active:bg-dark-text-primary dark:active:text-dark-background':
                    variant === 'outline-toned',
                'pointer-events-none cursor-not-allowed opacity-50 dark:opacity-40':
                    disabled,
            },
        ]"
        :style="buttonStyle"
        :disabled="disabled"
    >
        <a v-if="href" :href="href" class="flex items-center gap-2">
            <v-icon
                v-if="icon"
                :icon="icon"
                size="small"
                :class="iconClasses"
            ></v-icon>
            <slot v-if="$slots.default"></slot>
            <span v-else>{{ text }}</span>
        </a>
        <template v-else>
            <v-icon
                v-if="icon"
                :icon="icon"
                size="small"
                :class="iconClasses"
            ></v-icon>
            <slot v-if="$slots.default"></slot>
            <span v-else>{{ text }}</span>
        </template>
    </button>
</template>

<script setup>
import { computed, useSlots } from "vue";

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
        default: "primary",
        validator: (value) =>
            [
                "primary",
                "secondary",
                "danger",
                "success",
                "outline-primary",
                "outline-secondary",
                "outline-danger",
                "outline-success",
                "outline-toned",
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
});

const slots = useSlots();

const buttonStyle = computed(() => {
    const solidVariants = ["primary", "secondary", "danger", "success"];
    if (props.color && solidVariants.includes(props.variant)) {
        return { backgroundColor: props.color };
    }
    return {};
});

const iconClasses = computed(() => {
    const classes = {};
    if (props.variant === "outline-primary") {
        classes["text-[#193CB8] dark:text-dark-primary"] = true;
    } else if (props.variant === "outline-secondary") {
        classes["text-brand-secondary dark:text-[#b166ff]"] = true;
    } else if (props.variant === "outline-danger") {
        classes["text-danger dark:text-dark-status-red"] = true;
    } else if (props.variant === "outline-success") {
        classes["text-success dark:text-dark-status-green"] = true;
    } else if (props.variant === "outline-toned") {
        classes[
            "text-toned dark:text-dark-text-secondary group-hover:text-white dark:group-hover:text-dark-background"
        ] = true;
    }
    return classes;
});
</script>
