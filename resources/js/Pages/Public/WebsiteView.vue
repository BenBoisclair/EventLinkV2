<script setup lang="ts">
import WebsiteRenderer from '@/Components/WebsiteBuilder/Renderer/WebsiteRenderer.vue';
import type { HeroBlockProps } from '@/types/blocks';
import { Event } from '@/types/event';
import type {
    Block,
    ThemeColors,
    ThemeOptions,
    Widget,
} from '@/types/websiteBuilder';
import { createDefaultThemes } from '@/utils/themes';
import Hotjar from '@hotjar/browser';
import { useHead } from '@vueuse/head';
import { computed, onMounted } from 'vue';

interface WebsiteData {
    id: string;
    settings: {
        widgets?: Widget[];
        colorTheme?: {
            selectedTheme: ThemeOptions;
            themes: Record<ThemeOptions, ThemeColors>;
        };
        design?: {
            selectedFont: string;
            // Add other design settings later if needed
        };
        metadata?: {
            title?: string;
            description?: string;
        };
    };
    blocks: Block[];
    event?: Event;
    is_published: boolean;
    favicon_url?: string | null;
}

const { website } = defineProps<{ website: WebsiteData }>();

const widgets = computed(() => website.settings?.widgets || []);

const colorTheme = computed(() => {
    const themeData = website.settings?.colorTheme;
    return (
        themeData ?? {
            selectedTheme: 'blue' as ThemeOptions,
            themes: createDefaultThemes(),
        }
    );
});

const selectedFont = computed(() => {
    return website.settings?.design?.selectedFont || 'Inter'; // Default to Inter
});

const blocks = computed((): Block[] => {
    if (!website?.blocks) {
        return [];
    }

    return website.blocks.filter((block) => !!block.type);
});

const eventName = computed(() => website.event?.name || 'Event Website');

const eventForRenderer = computed(() => {
    return website.event ? { ...website.event } : undefined;
});

const metaTitle = computed(
    () => website.settings?.metadata?.title || eventName.value,
);
const metaDescription = computed(
    () => website.settings?.metadata?.description || '',
);
const faviconUrl = computed(() => {
    // Prepend /storage/ if it's a relative path
    if (website.favicon_url && !website.favicon_url.startsWith('http')) {
        return `/storage/${website.favicon_url}`;
    }
    return website.favicon_url;
});

const socialImageUrl = computed(() => {
    const heroBlock = blocks.value.find((block) => block.type === 'hero');
    let imageUrl = null;

    // Attempt to get image from Hero block props
    if (heroBlock && heroBlock.type === 'hero') {
        // Assert props type after confirming block type
        imageUrl = (heroBlock.props as HeroBlockProps)?.imageUrl ?? null;
    }

    // Fallback to favicon if no hero image
    if (!imageUrl) {
        imageUrl = faviconUrl.value;
    }

    // Prepend /storage/ if it's a relative path and not null/empty
    if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
        return `/storage/${imageUrl}`;
    } else if (imageUrl && imageUrl.startsWith('/storage/')) {
        // Already correctly formatted
        return imageUrl;
    } else if (imageUrl && imageUrl.startsWith('http')) {
        // Absolute URL
        return imageUrl;
    }

    // If still null or empty after checks, return null
    return imageUrl || null; // Ensure null is returned if no valid image found
});

const pageUrl = computed(() =>
    typeof window !== 'undefined' ? window.location.href : '',
);

useHead({
    htmlAttrs: {
        lang: 'en', // Assuming English, adjust if needed
    },
    title: metaTitle,
    link: [
        // Favicon
        ...(faviconUrl.value ? [{ rel: 'icon', href: faviconUrl.value }] : []),
        // Canonical URL
        ...(pageUrl.value ? [{ rel: 'canonical', href: pageUrl.value }] : []),
    ],
    meta: [
        // Basic Meta
        {
            name: 'description',
            content: metaDescription,
        },
        // Open Graph
        { property: 'og:title', content: metaTitle },
        { property: 'og:description', content: metaDescription },
        ...(pageUrl.value
            ? [{ property: 'og:url', content: pageUrl.value }]
            : []),
        ...(socialImageUrl.value
            ? [{ property: 'og:image', content: socialImageUrl.value }]
            : []),
        // Twitter Card
        { name: 'twitter:card', content: 'summary_large_image' }, // Use 'summary' if images are small
        { name: 'twitter:title', content: metaTitle },
        { name: 'twitter:description', content: metaDescription },
        ...(socialImageUrl.value
            ? [{ name: 'twitter:image', content: socialImageUrl.value }]
            : []),
    ],
});

// Tag Hotjar recording on mount (only in production)
onMounted(() => {
    if (import.meta.env.PROD) {
        const tags: string[] = [];
        if (website.id) {
            tags.push(`website:${website.id}`);
        }
        if (website.event?.id) {
            tags.push(`event:${website.event.id}`);
        }
        if (tags.length > 0) {
            // @ts-ignore - tagRecording exists but might be missing from older types
            Hotjar.tagRecording(tags);
        }
    }
});
</script>

<template>
    <WebsiteRenderer
        :blocks="blocks"
        :widgets="widgets"
        :color-theme="colorTheme"
        :event="eventForRenderer"
        :is-editor-mode="false"
        :selected-font="selectedFont"
        class="min-h-screen"
    />
</template>
