<template>
    <Section title="Danger Zone">
        <p class="mt-1 text-sm dark:text-dark-text-secondary text-text-muted">
            Permanently delete this event and all associated data.
        </p>
        <div
            class="p-4 mt-4 border border-red-100 rounded-md dark:border-dark-status-red/30"
        >
            <p class="font-medium text-red-600 dark:text-dark-status-red">
                Delete Event
            </p>
            <p
                class="mt-1 text-sm dark:text-dark-text-secondary text-text-muted"
            >
                Permanently delete this event and all associated data including attendees, exhibitors, and website content.
                This action cannot be undone.
            </p>
            <Button
                text="Delete Event"
                variant="danger"
                class="w-full mt-3"
                @click="showDeleteEventModal = true"
            />
        </div>

        <!-- Delete Event Confirmation Modal -->
        <Modal
            v-model="showDeleteEventModal"
            title="Delete Event?"
            :closeOnClickOutside="true"
        >
            <p class="mt-4 text-sm">
                Are you sure you want to delete this event? This action cannot
                be undone.
            </p>

            <p
                class="mt-2 text-sm dark:text-dark-text-secondary text-text-muted"
            >
                All event data including attendees, exhibitors, and website content will be
                permanently removed. You will be redirected to the events list.
            </p>

            <div class="flex gap-3 mt-6">
                <Button
                    text="Cancel"
                    variant="primary"
                    class="flex-1"
                    @click="showDeleteEventModal = false"
                />
                <Button
                    :text="isDeletingEvent ? 'Deleting...' : 'Yes, Delete Event'"
                    variant="outline-danger"
                    icon="$trashCanOutline"
                    class="flex-1"
                    :disabled="isDeletingEvent"
                    @click="confirmDeleteEvent"
                />
            </div>
        </Modal>
    </Section>
</template>

<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import Modal from "@/Components/UI/Modal.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps({
    eventId: {
        type: Number,
        required: true,
    },
});

// Event deletion
const showDeleteEventModal = ref(false);
const isDeletingEvent = ref(false);

const confirmDeleteEvent = () => {
    if (!props.eventId) {
        console.error("Event ID is missing, cannot delete.");
        showDeleteEventModal.value = false;
        return;
    }

    isDeletingEvent.value = true;

    router.delete(
        route("organiser.event.destroy", props.eventId),
        {
            onFinish: () => {
                // No need to reset state since we're redirecting
            },
            onError: (errors) => {
                console.error("Error deleting event:", errors);
                isDeletingEvent.value = false;
                showDeleteEventModal.value = false;
            },
        }
    );
};
</script>