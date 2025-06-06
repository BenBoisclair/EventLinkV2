<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";
import Pill from "@/Components/UI/Pill.vue";
import { defineProps, defineEmits, computed } from "vue";

const props = defineProps({
    state: {
        type: Object,
        required: true,
    },
    isShowingPublishedConfirmation: Boolean,
    isUnpublishing: Boolean,
    shareUrl: {
        type: String,
        required: true,
    },
    copyStatus: {
        type: String,
        required: true,
    },
    isShareModalOpen: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits([
    "publish",
    "unpublish",
    "copy",
    "update:is-share-modal-open",
]);

function handleClick() {
    if (props.state.text === "Publish") {
        emit("publish");
    } else if (props.state.text === "Unpublish") {
        emit("unpublish");
    }
}

function closeModal() {
    emit("update:is-share-modal-open", false);
}

function handleCopy() {
    emit("copy");
}

const shareModalOpen = computed({
    get: () => props.isShareModalOpen,
    set: (value) => emit("update:is-share-modal-open", value),
});
</script>

<template>
    <div>
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

        <Modal
            v-model="shareModalOpen"
            title="Your website has been published!"
            :close-onclick-outside="true"
            max-width="max-w-lg"
            @close="closeModal"
        >
            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-dark-text-secondary">
                    Share this link with your attendees
                </p>
                <div
                    class="flex items-center p-2 space-x-2 border rounded-md dark:bg-dark-surface-elevated dark:border-dark-border bg-gray-50"
                >
                    <input
                        type="text"
                        readonly
                        :value="shareUrl"
                        class="flex-grow p-1 text-sm bg-transparent border-none dark:text-dark-text-primary focus:ring-0"
                        aria-label="Event Website Link"
                    />
                    <Button
                        :text="''"
                        :aria-label="
                            copyStatus === 'copied' ? 'Copied link' : 'Copy link'
                        "
                        :icon="copyStatus === 'copied' ? '$check' : '$contentCopy'"
                        :variant="copyStatus === 'copied' ? 'success' : 'secondary'"
                        @click="handleCopy"
                        :disabled="copyStatus === 'copied'"
                    />
                </div>
                <p
                    v-if="copyStatus === 'error'"
                    class="text-xs text-red-600 dark:text-dark-status-red"
                >
                    Could not copy link. Please copy it manually.
                </p>
            </div>
        </Modal>
    </div>
</template>
