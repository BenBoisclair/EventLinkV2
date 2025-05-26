<template>
    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-[36px] font-bold text-primary">Your website</h1>
            <p class="text-[14px] text-text-muted">
                Manage the website for your event
            </p>
            <p
                v-if="hasWebsite && lastEdited"
                class="mt-1 text-[12px] text-text-dimmed"
            >
                Last edited {{ lastEdited }}
            </p>
        </div>

        <template v-if="isLoading">
            <Section>
                <p>Loading website data...</p>
            </Section>
        </template>
        <template v-else-if="error">
            <Section>
                <p class="text-red-500">{{ error }}</p>
            </Section>
        </template>
        <template v-else-if="hasWebsite">
            <!-- Website Builder Card -->
            <Section
                class="bg-gradient-to-br from-primary/10 to-primary/5 dark:from-primary/20 dark:to-primary/10 border-primary/20"
            >
                <div class="flex flex-col items-center gap-8 lg:flex-row">
                    <!-- Content Section -->
                    <div class="flex-1">
                        <h2 class="mb-3 text-2xl font-bold text-primary">
                            Website Builder
                        </h2>
                        <p class="mb-6 text-text-muted">
                            Create a professional event website with our
                            intuitive drag-and-drop builder. No coding required!
                        </p>

                        <div class="flex flex-col gap-4 mb-6 sm:flex-row">
                            <Button
                                :href="
                                    route('website.builder', {
                                        website: props?.website?.slug,
                                    })
                                "
                                text="Open Builder"
                                variant="primary"
                                size="lg"
                                icon="$edit"
                            />
                            <div class="relative group">
                                <Button
                                    :href="
                                        props?.website?.is_published
                                            ? `/site/${props.website.slug}`
                                            : undefined
                                    "
                                    target="_blank"
                                    text="View Live Site"
                                    variant="secondary"
                                    icon="$link"
                                    :disabled="!props?.website?.is_published"
                                    class="w-full sm:w-auto"
                                />
                                <div
                                    v-if="!props?.website?.is_published"
                                    class="absolute px-3 py-1 mb-2 text-sm text-white transition-opacity transform -translate-x-1/2 bg-gray-900 rounded opacity-0 pointer-events-none bottom-full left-1/2 dark:bg-gray-700 group-hover:opacity-100 whitespace-nowrap"
                                >
                                    Publish your website first
                                </div>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/20"
                                >
                                    <svg
                                        class="w-4 h-4 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                                        />
                                    </svg>
                                </div>
                                <span class="text-text-dimmed"
                                    >Drag & Drop</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/20"
                                >
                                    <svg
                                        class="w-4 h-4 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-text-dimmed">Responsive</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/20"
                                >
                                    <svg
                                        class="w-4 h-4 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-text-dimmed">Real-time</span>
                            </div>
                        </div>
                    </div>

                    <!-- Visual Element -->
                    <div class="lg:w-[300px] flex-shrink-0">
                        <div class="relative">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-primary/20 to-primary/10 blur-xl"
                            ></div>
                            <div
                                class="relative p-4 rounded-lg shadow-lg bg-surface dark:bg-dark-surface"
                            >
                                <div class="space-y-3">
                                    <div
                                        class="h-8 bg-gray-200 rounded dark:bg-gray-700 animate-pulse"
                                    ></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div
                                            class="h-20 bg-gray-200 rounded dark:bg-gray-700 animate-pulse"
                                        ></div>
                                        <div
                                            class="h-20 bg-gray-200 rounded dark:bg-gray-700 animate-pulse"
                                        ></div>
                                    </div>
                                    <div
                                        class="h-12 bg-gray-200 rounded dark:bg-gray-700 animate-pulse"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Section>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Favicon Section -->
                <Favicon
                    v-if="props.event?.id && props.website?.id"
                    :event-id="props.event.id"
                    :website-id="props.website.id"
                    :current-favicon-url="props.website.favicon_url"
                    :meta-title="
                        props.website.settings?.metadata?.title ||
                        'Your Website Title'
                    "
                    :website-slug="props.website.slug"
                    @refresh-website="refreshWebsite"
                />
            </div>

            <MetaSection
                v-if="props.event?.id && props.website?.id"
                :event-id="props.event.id"
                :website-id="props.website.id"
                :current-meta-title="
                    props.website.settings?.metadata?.title || ''
                "
                :current-meta-description="
                    props.website.settings?.metadata?.description || ''
                "
            />
        </template>
        <template v-else>
            <Section title="Create your website">
                <CreatingWebsiteState v-if="isCreatingWebsite" />
                <NoWebsiteState
                    v-else
                    :disabled="isCreatingWebsite"
                    @create-website="createWebsite"
                />
            </Section>
        </template>
    </div>
</template>

<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import Favicon from "@/Components/WebsiteBuilder/Settings/Favicon.vue";
import MetaSection from "@/Components/WebsiteBuilder/Settings/MetaSection.vue";
import CreatingWebsiteState from "@/Components/WebsiteBuilder/States/CreatingWebsiteState.vue";
import NoWebsiteState from "@/Components/WebsiteBuilder/States/NoWebsiteState.vue";
import { formatRelativeTime } from "@/utils/date";
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import type { EventType } from "@/types/event";
import type { WebsiteType } from "@/types/website";

const props = defineProps<{
    event: EventType;
    website: WebsiteType | null;
}>();

const isLoading = ref<boolean>(false);
const error = ref<string | null>(null);
const isCreatingWebsite = ref<boolean>(false);

const hasWebsite = computed(() => !!props.website);

const createWebsite = async () => {
    if (!props.event?.id) {
        error.value = "Cannot create website without an event ID.";
        return;
    }
    isCreatingWebsite.value = true;
    await new Promise((resolve) => setTimeout(resolve, 3000));
    try {
        router.post(
            route("website.create", { event: props.event.id }),
            {},
            {
                onSuccess: () => {
                    error.value = null;
                },
                onError: (err) => {
                    error.value = err.message || "Error creating website.";
                },
                onFinish: () => {
                    isCreatingWebsite.value = false;
                },
            }
        );
    } catch (err: any) {
        error.value = err.message || "Error creating website.";
        isCreatingWebsite.value = false;
    }
};

const lastEdited = computed(() => {
    if (!props.website?.updated_at) return "Never";
    return formatRelativeTime(props.website.updated_at);
});

const refreshWebsite = () => {
    router.reload({ only: ["website"] });
};
</script>
