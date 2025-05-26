<template>
    <div class="flex flex-col gap-component p-section">
        <div class="flex items-center justify-between">
            <div>
                <h1
                    class="font-bold dark:text-dark-primary text-heading-primary text-primary"
                >
                    Attendees
                </h1>
                <p
                    class="dark:text-dark-text-secondary text-body text-text-muted"
                >
                    Manage your event attendees
                </p>
            </div>
            <Button
                text="Add Attendee"
                icon="$plus"
                iconType="mdi"
                variant="primary"
                @click="openAddAttendeeModal"
            />
        </div>

        <Spacer />

        <div>
            <div class="flex items-center justify-between">
                <h2
                    class="font-bold dark:text-dark-primary text-heading-secondary text-primary"
                >
                    Attendee List
                </h2>
                <Button
                    icon="$refresh"
                    variant="outline-toned"
                    @click="refreshAttendees"
                    title="Refresh attendee list"
                />
            </div>

            <Input
                v-model="searchQuery"
                placeholder="Search Attendees"
                icon="$magnify"
                iconType="mdi"
                class="mt-small max-w-input"
            />

            <!-- Attendee Table -->
            <div class="mt-4">
                <AttendeeTable
                    :attendees="filteredAttendees"
                    @edit="handleEditAttendee"
                    @delete="handleDeleteAttendee"
                    @viewCustomFields="handleViewCustomFields"
                />
            </div>
        </div>

        <!-- Add/Edit Attendee Modal -->
        <Modal
            v-model="showAddAttendeeModal"
            :title="editingAttendee ? 'Edit Attendee' : 'Add Attendee'"
        >
            <form @submit.prevent="handleSubmitAttendee" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="first_name" value="First Name" />
                        <Input
                            id="first_name"
                            v-model="attendeeForm.first_name"
                            type="text"
                            class="w-full mt-1"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="attendeeForm.errors.first_name"
                        />
                    </div>
                    <div>
                        <InputLabel for="last_name" value="Last Name" />
                        <Input
                            id="last_name"
                            v-model="attendeeForm.last_name"
                            type="text"
                            class="w-full mt-1"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="attendeeForm.errors.last_name"
                        />
                    </div>
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <Input
                        id="email"
                        v-model="attendeeForm.email"
                        type="email"
                        class="w-full mt-1"
                        required
                    />
                    <InputError
                        class="mt-2"
                        :message="attendeeForm.errors.email"
                    />
                </div>

                <div class="flex justify-end gap-2">
                    <Button
                        text="Cancel"
                        variant="secondary"
                        @click="cancelAttendeeModal"
                    />
                    <Button
                        type="submit"
                        text="Save"
                        variant="primary"
                        :disabled="attendeeForm.processing"
                    />
                </div>
            </form>
        </Modal>

        <!-- Custom Fields Modal -->
        <Modal
            v-model="showCustomFieldsModal"
            title="Additional Information"
            :close-on-click-outside="true"
        >
            <div v-if="selectedAttendee" class="space-y-6">
                <!-- Attendee Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-300 font-semibold text-lg">
                                {{ selectedAttendee.first_name.charAt(0) }}{{ selectedAttendee.last_name.charAt(0) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-dark-text-primary">
                                {{ selectedAttendee.first_name }} {{ selectedAttendee.last_name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-dark-text-secondary">
                                {{ selectedAttendee.email }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Custom Fields -->
                <div
                    v-if="
                        selectedAttendee.custom_fields &&
                        Object.keys(parsedCustomFields).length > 0
                    "
                    class="space-y-4"
                >
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Form Responses
                    </h4>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                        <div
                            v-for="(value, key) in parsedCustomFields"
                            :key="key"
                            class="flex flex-col space-y-1"
                        >
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                {{ formatFieldName(key) }}
                            </div>
                            <div class="text-gray-900 dark:text-dark-text-primary bg-white dark:bg-gray-700 px-3 py-2 rounded border border-gray-200 dark:border-gray-600">
                                {{ value || 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div
                    v-else
                    class="py-12 text-center"
                >
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-dark-text-secondary">
                        No additional information available
                    </p>
                    <p class="text-sm text-gray-400 dark:text-gray-600 mt-1">
                        Custom fields will appear here when available
                    </p>
                </div>

                <!-- Registration Details -->
                <div v-if="selectedAttendee.created_at" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Registered on</span>
                        <span class="text-gray-700 dark:text-gray-300">
                            {{ new Date(selectedAttendee.created_at).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) }}
                        </span>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import AttendeeTable from '@/Components/Attendees/AttendeeTable.vue';
import Input from '@/Components/Forms/Input.vue';
import InputError from '@/Components/Forms/InputError.vue';
import InputLabel from '@/Components/Forms/InputLabel.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import Spacer from '@/Components/UI/Spacer.vue';
import { useWebsiteBuilderStore } from '@/stores/websiteBuilderStore';
import type { Attendee } from '@/types/attendee';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// Define an interface for the Event prop
interface Event {
    id: number;
    name: string;
    [key: string]: any; // Keep the index signature if needed
}

const props = defineProps<{
    attendees: Attendee[];
    event: Event; // Use the defined interface
}>();

const searchQuery = ref('');
const showAddAttendeeModal = ref(false);
const editingAttendee = ref<Attendee | null>(null);
const showCustomFieldsModal = ref(false);
const selectedAttendee = ref<Attendee | null>(null);

const attendeeForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
});

const websiteBuilderStore = useWebsiteBuilderStore();

// Calculate statistics based on the prop data
const statistic = computed(() => ({
    name: 'Total Attendees',
    count: props.attendees.length,
    icon: '$accountGroupOutline',
    title: 'Attendees',
}));

// Computed property to filter attendees based on search query
const filteredAttendees = computed(() => {
    if (!props.attendees) return [];

    let filtered = props.attendees;

    // Filter by search query (checking name, email, company, and custom fields)
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter((attendee) => {
            const name =
                `${attendee.first_name} ${attendee.last_name}`.toLowerCase();
            const email = attendee.email.toLowerCase();
            const company = attendee.company?.toLowerCase() || '';

            // Check custom fields
            const customFieldsMatch = attendee.custom_fields
                ? Object.values(attendee.custom_fields).some((value) =>
                      String(value).toLowerCase().includes(query),
                  )
                : false;

            return (
                name.includes(query) ||
                email.includes(query) ||
                company.includes(query) ||
                customFieldsMatch
            );
        });
    }

    return filtered;
});

const handleEditAttendee = (attendee: Attendee) => {
    editingAttendee.value = attendee;
    attendeeForm.reset(); // Reset form including errors
    attendeeForm.first_name = attendee.first_name;
    attendeeForm.last_name = attendee.last_name;
    attendeeForm.email = attendee.email;
    attendeeForm.clearErrors(); // Clear any validation errors
    showAddAttendeeModal.value = true;
};

const handleDeleteAttendee = (attendee: Attendee) => {
    router.delete(
        route('organiser.event.attendees.destroy', {
            event: props.event.id,
            attendee: attendee.id,
        }),
    );
};

const handleViewCustomFields = (attendee: Attendee) => {
    selectedAttendee.value = attendee;
    showCustomFieldsModal.value = true;
};

const handleSubmitAttendee = () => {
    if (editingAttendee.value) {
        attendeeForm.patch(
            route('organiser.event.attendees.update', {
                event: props.event.id,
                attendee: editingAttendee.value.id,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    showAddAttendeeModal.value = false;
                    editingAttendee.value = null;
                    attendeeForm.reset();
                },
            },
        );
    } else {
        attendeeForm.post(
            route('organiser.event.attendees.store', {
                event: props.event.id,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    showAddAttendeeModal.value = false;
                    attendeeForm.reset();
                },
            },
        );
    }
};

const parsedCustomFields = computed(() => {
    if (!selectedAttendee.value?.custom_fields) return {};

    try {
        if (typeof selectedAttendee.value.custom_fields === 'string') {
            const parsed = JSON.parse(selectedAttendee.value.custom_fields);
            return parsed.custom_fields || parsed;
        }
        return selectedAttendee.value.custom_fields;
    } catch (e) {
        return {};
    }
});

const formatFieldName = (key: string) => {
    return key
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const cancelAttendeeModal = () => {
    showAddAttendeeModal.value = false;
    editingAttendee.value = null;
    attendeeForm.reset();
    attendeeForm.clearErrors();
};

const openAddAttendeeModal = () => {
    editingAttendee.value = null;
    attendeeForm.reset();
    attendeeForm.clearErrors();
    showAddAttendeeModal.value = true;
};

const refreshAttendees = () => {
    router.reload({
        only: ['attendees'],
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
