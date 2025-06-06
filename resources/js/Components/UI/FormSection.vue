<script setup>
import Section from './Section.vue';

const emit = defineEmits(['submitted']);

const hasActions = !!$slots.actions;

const onSubmit = () => {
    emit('submitted');
};
</script>

<template>
    <Section>
        <template #title>
            <slot name="title" />
        </template>

        <template #description>
            <slot name="description" />
        </template>

        <template #content>
            <form @submit.prevent="onSubmit">
                <div class="grid grid-cols-6 gap-6">
                    <slot name="form" />
                </div>

                <div v-if="hasActions" class="flex items-center justify-end px-4 py-3 bg-gray-50 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                    <slot name="actions" />
                </div>
            </form>
        </template>
    </Section>
</template>