<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputError from "@/Components/Forms/InputError.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Textarea from "@/Components/Forms/Textarea.vue";
import Button from "@/Components/UI/Button.vue";
import Container from "@/Components/UI/Container.vue";
import Section from "@/Components/UI/Section.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import type { PageProps } from "@/types";
import { useForm, usePage, router } from "@inertiajs/vue3";

const page = usePage<PageProps>();
const form = useForm({
    name: "",
    description: "",
    start_date: "",
    end_date: "",
    team_id: page.props.auth.user.current_team?.id,
    user_id: page.props.auth.user.id,
});

const submit = () => {
    form.post(route("organiser.events.store"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <DashboardLayout title="Create Event">
        <div class="dark:bg-dark-background flex flex-1 bg-[#F1F5F9]">
            <div class="flex-1 pt-5 overflow-y-scroll">
                <Container>
                    <div class="flex flex-col gap-6">
                        <div>
                            <h1
                                class="text-2xl font-bold dark:text-dark-primary text-primary"
                            >
                                Create an Event
                            </h1>
                            <p
                                class="text-sm dark:text-dark-text-secondary text-text-muted"
                            >
                                Enter the details for your new event.
                            </p>
                        </div>

                        <Section title="Event Details">
                            <form
                                @submit.prevent="submit"
                                class="mt-4 space-y-4"
                            >
                                <div>
                                    <InputLabel
                                        forInput="eventName"
                                        value="Event Name *"
                                    />
                                    <Input
                                        id="eventName"
                                        v-model="form.name"
                                        type="text"
                                        class="w-full mt-1"
                                        required
                                        autofocus
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.name"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        forInput="eventDescription"
                                        value="Description"
                                    />
                                    <Textarea
                                        id="eventDescription"
                                        v-model="form.description"
                                        class="mt-1"
                                        :rows="4"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.name"
                                    />
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div>
                                        <InputLabel
                                            forInput="startDate"
                                            value="Start Date"
                                        />
                                        <Input
                                            id="startDate"
                                            v-model="form.start_date"
                                            type="date"
                                            class="w-full mt-1"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.description"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel
                                            forInput="endDate"
                                            value="End Date"
                                        />
                                        <Input
                                            id="endDate"
                                            v-model="form.end_date"
                                            type="date"
                                            class="w-full mt-1"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.end_date"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <Button
                                        text="Create Event"
                                        variant="primary"
                                        type="submit"
                                        :disabled="form.processing"
                                        :class="{
                                            'opacity-25': form.processing,
                                        }"
                                    />
                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out dark:text-dark-text-secondary"
                                        leave-to-class="opacity-0"
                                    >
                                        <p
                                            v-if="form.recentlySuccessful"
                                            class="text-sm text-gray-600 dark:text-dark-text-tertiary"
                                        >
                                            Event Created! Redirecting...
                                        </p>
                                    </Transition>
                                </div>
                            </form>
                        </Section>
                    </div>
                </Container>
            </div>
        </div>
    </DashboardLayout>
</template>
