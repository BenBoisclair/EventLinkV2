<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Pill from "@/Components/UI/Pill.vue";
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    state: {
        type: Object,
        required: true,
    },
    isShowingPublishedConfirmation: Boolean,
    isUnpublishing: Boolean,
});

const emit = defineEmits(["publish", "unpublish"]);

function handleClick() {
    if (props.state.text === "Publish") {
        emit("publish");
    } else if (props.state.text === "Unpublish") {
        emit("unpublish");
    }
}
</script>

<template>
    <div class="flex items-center gap-2">
        <Button
            :text="state.text"
            :icon="state.icon"
            :icon-type="'mdi'"
            :variant="state.variant"
            :disabled="state.disabled"
            @click="handleClick"
            :class="{
                'border-green-500 !text-green-500':
                    isShowingPublishedConfirmation,
            }"
        />
    </div>
</template>
