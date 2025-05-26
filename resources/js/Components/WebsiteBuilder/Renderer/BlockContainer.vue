<script setup lang="ts">
interface BlockContainerProps {
    id: string;
    backgroundColor?: string;
    isEditorMode?: boolean;
    device?: 'mobile' | 'tablet' | 'desktop';
}

const props = withDefaults(defineProps<BlockContainerProps>(), {
    backgroundColor: '#FFFFFF',
    isEditorMode: false,
    device: 'desktop',
});

defineEmits<{
    (e: 'delete', blockId: string): void;
    (e: 'updateBlock', blockId: string, newProps: Record<string, any>): void;
    (e: 'edit'): void;
}>();
</script>

<template>
    <div
        class="relative w-full group"
        :style="{ backgroundColor: props.backgroundColor }"
    >
        <slot />

        <div
            v-if="props.isEditorMode"
            class="absolute z-10 flex hidden gap-2 left-4 top-4 group-hover:flex"
        >
            <button
                @click="$emit('edit')"
                class="flex items-center justify-center gap-0.5 rounded-full bg-warning/30 p-2 hover:bg-warning/70"
                title="Edit block"
            >
                <v-icon icon="$squareEditOutline" size="small" color="white" />
                <span class="text-sm text-white">Edit</span>
            </button>

            <button
                @click="$emit('delete', props.id)"
                class="flex items-center justify-center p-2 text-white rounded-full bg-danger/30 hover:bg-danger/70"
                title="Delete block"
            >
                <v-icon icon="$trashCanOutline" size="small" color="white" />
            </button>
        </div>
    </div>
</template>
