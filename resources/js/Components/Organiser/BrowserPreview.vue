<template>
    <div class="rounded-lg" :class="[isDark ? 'bg-[#0F172B]' : 'bg-[#E2E8F0]']">
        <div class="pt-10 pl-10">
            <div
                class="flex flex-col w-full h-full overflow-hidden rounded-tl-2xl"
                :class="[isDark ? 'bg-[#3A3B3D]' : 'bg-[#FFFFFF]']"
            >
                <div class="flex h-[40px] w-full">
                    <div
                        class="flex h-full w-[90px] items-center justify-center gap-1.5 rounded-br-3xl pr-2"
                        :class="[isDark ? 'bg-[#1F2020]' : 'bg-[#DDE1E5]']"
                    >
                        <div
                            class="h-[16px] w-[16px] rounded-full bg-[#FF534B]"
                        ></div>
                        <div
                            class="h-[16px] w-[16px] rounded-full bg-[#FDB42B]"
                        ></div>
                        <div
                            class="h-[16px] w-[16px] rounded-full bg-[#1FC338]"
                        ></div>
                    </div>
                    <div
                        class="min-w-0 max-w-[240px] flex-1"
                        :class="[isDark ? 'bg-[#1F2020]' : 'bg-[#DDE1E5]']"
                    >
                        <div
                            class="flex items-center h-full min-w-0 gap-2 px-3 rounded-tl-2xl"
                            :class="[isDark ? 'bg-[#3A3B3D]' : 'bg-[#FFFFFF]']"
                        >
                            <img
                                v-if="faviconUrl"
                                :src="faviconUrl"
                                :key="faviconUrl"
                                alt="Favicon Preview"
                                class="flex-shrink-0 object-contain w-6 h-6 rounded-sm"
                                @error="setDefaultFaviconOnError"
                            />
                            <span
                                class="text-sm truncate"
                                :class="[
                                    isDark ? 'text-gray-300' : 'text-black',
                                ]"
                            >
                                {{ title }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-2 flex h-[30px] items-center gap-3">
                    <div class="flex items-center gap-3 ml-3">
                        <img
                            src="/icons/back.svg"
                            alt="Back"
                            class="h-3.5 w-3.5"
                        />
                        <img
                            src="/icons/forward.svg"
                            alt="Forward"
                            class="h-3.5 w-3.5"
                        />
                        <img
                            src="/icons/refresh.svg"
                            alt="Refresh"
                            class="h-3.5 w-3.5"
                        />
                    </div>
                    <div
                        class="flex items-center flex-1 h-full min-w-0 px-3 rounded-tl-lg rounded-bl-lg"
                        :class="[isDark ? 'bg-[#1F2020]' : 'bg-[#DDE1E5]']"
                    >
                        <span
                            class="w-full text-sm text-left truncate"
                            :class="[isDark ? 'text-gray-300' : 'text-black']"
                        >
                            {{ url }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

interface Props {
    title: string;
    url: string;
    faviconUrl?: string;
    isDark?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isDark: false,
    faviconUrl: undefined,
});


const setDefaultFaviconOnError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (target.src !== window.location.origin + '/favicon_placeholder.png') {
        target.src = '/favicon_placeholder.png';
    }
};
</script>
