<template>
    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-[36px] font-bold text-primary">Your website</h1>
            <p class="text-[14px] text-text-muted">
                Manage the website for your event
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
                <div class="flex flex-col gap-8">
                    <div class="flex-1">
                        <h2 class="mb-3 text-2xl font-bold text-primary">
                            Website Builder
                        </h2>

                        <div class="flex flex-col gap-4 sm:flex-row">
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
