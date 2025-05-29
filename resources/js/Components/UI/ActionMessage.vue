<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    on: Boolean,
    duration: {
        type: Number,
        default: 3000,
    },
});

const show = ref(false);

watch(() => props.on, (value) => {
    if (value) {
        show.value = true;
        setTimeout(() => {
            show.value = false;
        }, props.duration);
    }
});
</script>

<template>
    <div>
        <transition
            leave-active-class="transition ease-in duration-1000"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="show" class="text-sm text-gray-600">
                <slot />
            </div>
        </transition>
    </div>
</template>