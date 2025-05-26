<template>
    <div>
        <!-- Table -->
        <div
            class="mt-6 overflow-x-auto rounded-lg bg-surface dark:bg-dark-surface"
        >
            <table class="w-full">
                <thead>
                    <tr
                        class="dark:border-dark-border border-b border-[#E2E8F0]"
                    >
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Logo
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="exhibitor in exhibitors"
                        :key="exhibitor.id"
                        class="dark:border-dark-border border-b border-[#E2E8F0]"
                    >
                        <td class="px-6 py-4">
                            <img
                                v-if="exhibitor.logo_path"
                                :src="resolveStorageUrl(exhibitor.logo_path)"
                                :alt="`${exhibitor.name} Logo`"
                                class="object-contain w-10 h-10 rounded"
                            />
                            <span
                                v-else
                                class="text-xs text-gray-400 dark:text-dark-text-tertiary"
                                >No Logo</span
                            >
                        </td>
                        <td
                            class="px-6 py-4 text-sm dark:text-dark-text-primary"
                        >
                            {{ exhibitor.name ?? 'N/A' }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm dark:text-dark-text-primary"
                        >
                            {{ exhibitor.contact_email ?? 'N/A' }}
                        </td>
                        <td class="flex items-center gap-2 px-6 py-4">
                            <Button
                                variant="outline-primary"
                                text="Edit"
                                size="small"
                                @click="emitEdit(exhibitor)"
                            >
                                Edit
                            </Button>
                            <Button
                                variant="outline-danger"
                                text="Delete"
                                size="small"
                                @click="emitDelete(exhibitor)"
                            />
                        </td>
                    </tr>
                    <tr v-if="exhibitors.length === 0">
                        <td
                            colspan="4"
                            class="px-6 py-4 text-sm text-center text-gray-500 dark:text-dark-text-secondary"
                        >
                            No exhibitors found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal
            v-model="showDeleteModal"
            title="Confirm Deletion"
            :closeOnClickOutside="true"
            max-width="max-w-md"
        >
            <p v-if="exhibitorToDelete" class="dark:text-dark-text-primary">
                Are you sure you want to remove the exhibitor
                <strong>{{ exhibitorToDelete.name }}</strong> from this event?
                This action cannot be undone.
            </p>
            <div class="flex justify-end gap-3 mt-4">
                <Button
                    variant="outline-toned"
                    text="Cancel"
                    @click="cancelDelete"
                />
                <Button
                    variant="danger"
                    text="Delete Exhibitor"
                    @click="confirmDelete"
                />
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Exhibitor } from '@/types/exhibitor';
import { resolveStorageUrl } from '@/utils/storage';
import { ref } from 'vue';

const props = defineProps<{
    exhibitors: Exhibitor[];
}>();

// Define emits
const emit = defineEmits(['edit-exhibitor', 'delete-exhibitor']);

// Function to emit the edit event
const emitEdit = (exhibitor: Exhibitor) => {
    emit('edit-exhibitor', exhibitor);
};

// --- Delete Modal State and Logic ---
const showDeleteModal = ref(false);
const exhibitorToDelete = ref<Exhibitor | null>(null);

// Function to *initiate* the delete process (show modal)
const emitDelete = (exhibitor: Exhibitor) => {
    exhibitorToDelete.value = exhibitor;
    showDeleteModal.value = true;
};

// Function to confirm deletion (called by Modal button)
const confirmDelete = () => {
    if (exhibitorToDelete.value) {
        emit('delete-exhibitor', exhibitorToDelete.value);
    }
    cancelDelete(); // Close modal after emitting
};

// Function to cancel deletion (called by Modal button)
const cancelDelete = () => {
    showDeleteModal.value = false;
    exhibitorToDelete.value = null;
};
// --- End Delete Modal Logic ---
</script>
