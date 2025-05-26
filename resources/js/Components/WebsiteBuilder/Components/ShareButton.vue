<script setup lang="ts">
import { defineProps, defineEmits } from "vue";
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";

const props = defineProps({
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
    isPublished: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const emit = defineEmits(["copy", "update:is-share-modal-open"]);

function openModal() {
    emit("update:is-share-modal-open", true);
}
function closeModal() {
    emit("update:is-share-modal-open", false);
}
function handleCopy() {
    emit("copy");
}
</script>

<template>
    <Button
        text="Share"
        icon="$shareVariantOutline"
        icon-type="mdi"
        variant="outline-primary"
        @click="openModal"
        :disabled="!shareUrl || !isPublished"
    />
    <Modal
        v-model="props.isShareModalOpen"
        title="Share Website"
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
</template>
