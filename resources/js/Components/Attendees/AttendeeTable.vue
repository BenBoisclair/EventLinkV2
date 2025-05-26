<template>
    <div>
        <!-- Table -->
        <div
            class="mt-6 overflow-x-auto rounded-lg dark:bg-dark-surface bg-surface"
        >
            <table class="w-full">
                <thead>
                    <tr
                        class="dark:border-dark-border border-b border-[#E2E8F0]"
                    >
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Contact
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Submitted At
                        </th>
                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Custom Fields
                        </th>

                        <th
                            class="px-6 py-4 text-sm font-medium text-left dark:text-dark-text-secondary text-text-muted"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="attendee in attendees"
                        :key="attendee.id"
                        class="dark:border-dark-border border-b border-[#E2E8F0]"
                    >
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-dark-text-primary"
                                    >
                                        {{ attendee.first_name }}
                                        {{ attendee.last_name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-dark-text-primary"
                        >
                            {{ attendee.email }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-500 dark:text-dark-text-secondary"
                        >
                            {{ formatTimestamp(attendee.created_at) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div
                                v-if="
                                    attendee.custom_fields &&
                                    Object.keys(attendee.custom_fields).length >
                                        0
                                "
                            >
                                <Button
                                    text="View Custom Fields"
                                    variant="outline-primary"
                                    size="small"
                                    icon="$listBox"
                                    @click="$emit('viewCustomFields', attendee)"
                                />
                            </div>
                            <div
                                v-else
                                class="text-gray-400 dark:text-dark-text-tertiary"
                            >
                                No custom fields
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <Button
                                    text="Edit"
                                    variant="outline-primary"
                                    size="small"
                                    icon="$squareEditOutline"
                                    @click="$emit('edit', attendee)"
                                />
                                <Button
                                    text="Delete"
                                    variant="outline-danger"
                                    size="small"
                                    icon="$trashCanOutline"
                                    @click="openDeleteModal(attendee)"
                                />
                            </div>
                        </td>
                    </tr>
                    <tr v-if="attendees.length === 0">
                        <td
                            colspan="4"
                            class="px-6 py-4 text-sm text-center text-gray-500 dark:text-dark-text-secondary"
                        >
                            No attendees found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal
            v-model="showDeleteModal"
            title="Delete Attendee?"
            :closeOnClickOutside="true"
        >
            <p class="mt-4 text-sm dark:text-dark-text-primary">
                Are you sure you want to delete this attendee? This action
                cannot be undone.
            </p>

            <p
                class="mt-2 text-sm dark:text-dark-text-secondary text-text-muted"
            >
                All attendee data will be permanently removed.
            </p>

            <div class="flex gap-3 mt-6">
                <Button
                    text="Cancel"
                    variant="primary"
                    class="flex-1"
                    @click="showDeleteModal = false"
                />
                <Button
                    text="Yes, Delete"
                    variant="outline-danger"
                    icon="$trashCanOutline"
                    class="flex-1"
                    @click="confirmDelete"
                />
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { Attendee } from '@/types/attendee';
import { format } from 'date-fns';
import { ref } from 'vue';

defineProps<{
    attendees: Attendee[];
}>();

const emit = defineEmits<{
    (e: 'edit', attendee: Attendee): void;
    (e: 'delete', attendee: Attendee): void;
    (e: 'viewCustomFields', attendee: Attendee): void;
}>();

const showDeleteModal = ref(false);
const selectedAttendee = ref<Attendee | null>(null);

const openDeleteModal = (attendee: Attendee) => {
    selectedAttendee.value = attendee;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (selectedAttendee.value) {
        emit('delete', selectedAttendee.value);
        showDeleteModal.value = false;
        selectedAttendee.value = null;
    }
};

const formatTimestamp = (timestamp: string | undefined) => {
    if (!timestamp) return 'N/A';
    try {
        return format(new Date(timestamp), 'PPpp'); // Format: Sep 04, 2024, 2:30:00 PM
    } catch (e) {
        console.error('Error formatting date:', e);
        return timestamp; // Return original if formatting fails
    }
};
</script>
