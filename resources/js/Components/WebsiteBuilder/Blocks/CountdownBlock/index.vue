<script setup lang="ts">
import BlockButton from "@/Components/UI/BlockButton.vue";
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { CountdownBlockProps } from "@/types/blocks";
import type { Event } from "@/types/event";
import { getContrastingTextColor } from "@/utils/color";
import {
    computed,
    onMounted,
    onUnmounted,
    ref,
    watch,
    withDefaults,
} from "vue";
import BlockTitle from "../BlockTitle.vue";
import { useThemeColors } from "@/Composables/useThemeColors";

const props = withDefaults(
    defineProps<
        CountdownBlockProps & {
            useEventDates: boolean;
            event?: Event;
            showDays?: boolean;
            showHours?: boolean;
            showMinutes?: boolean;
            showSeconds?: boolean;
            finishedText?: string;
            buttonText?: string;
            buttonLink?: string;
            buttonEnabled?: boolean;
        }
    >(),
    {
        title: "Countdown",
        startDate: undefined,
        endDate: undefined,
        useEventDates: false,
        event: undefined,
        showDays: true,
        showHours: true,
        showMinutes: true,
        showSeconds: true,
        finishedText: "Event has started! 🎉",
        buttonText: "",
        buttonLink: "",
        buttonEnabled: false,
    }
);

const { colors } = useThemeColors();



// Theme-based styling
const computedBackgroundColor = computed(() => {
    return colors.value.backgroundPrimary;
});

const computedTextColor = computed(() => {
    return colors.value.textPrimary;
});

const unitBackgroundStyle = computed(() => ({
    backgroundColor: colors.value.backgroundSecondary,
}));

const computedButtonBackgroundColor = computed(() => {
    return colors.value.buttonPrimary;
});

const computedButtonTextColor = computed(() => {
    return colors.value.buttonPrimaryText;
});

const timeLeft = ref({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
});

const isCountdownFinished = computed(() => {
    return (
        timeLeft.value.days === 0 &&
        timeLeft.value.hours === 0 &&
        timeLeft.value.minutes === 0 &&
        timeLeft.value.seconds === 0
    );
});

let countdownInterval: number | null = null;

const calculateTimeLeft = () => {
    const now = new Date();
    let start: Date | null = null;

    if (props.useEventDates && props.event) {
        start = props.event.start_date
            ? new Date(props.event.start_date)
            : null;
    } else {
        start = props.startDate ? new Date(props.startDate) : null;
    }

    if (!start) {
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (countdownInterval) clearInterval(countdownInterval);
        return;
    }

    const difference = start.getTime() - now.getTime();

    if (difference > 0) {
        timeLeft.value = {
            days: Math.floor(difference / (1000 * 60 * 60 * 24)),
            hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
            minutes: Math.floor((difference / 1000 / 60) % 60),
            seconds: Math.floor((difference / 1000) % 60),
        };
    } else {
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (countdownInterval) clearInterval(countdownInterval);
    }
};


onMounted(() => {
    calculateTimeLeft();
    countdownInterval = window.setInterval(calculateTimeLeft, 1000);
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});

watch(
    [
        () => props.startDate,
        () => props.endDate,
        () => props.useEventDates,
        () => props.event?.start_date,
        () => props.event?.end_date,
    ],
    calculateTimeLeft
);
</script>

<template>
    <BlockContainer
        :background-color="colors.backgroundPrimary"
        class="flex min-h-[300px] flex-col items-center justify-center"
    >
        <div
            class="container w-full px-4 mx-auto"
            :style="{ color: computedTextColor }"
        >
            <BlockTitle
                v-if="!isCountdownFinished"
                :title="props.title"
                :title-color="computedTextColor"
                tag="h2"
                text-align="center"
                default-classes="mb-6 text-2xl font-bold md:mb-8 md:text-3xl"
            />
            <div
                v-if="!isCountdownFinished"
                class="flex flex-wrap items-center justify-center gap-3 text-center sm:gap-4"
            >
                <!-- Days -->
                <div
                    v-if="props.showDays"
                    class="p-3 rounded-lg min-w-20 sm:p-4"
                    :style="unitBackgroundStyle"
                >
                    <div class="text-3xl font-bold sm:text-4xl">
                        {{ timeLeft.days }}
                    </div>
                    <div class="text-xs sm:text-sm">Days</div>
                </div>
                <!-- Hours -->
                <div
                    v-if="props.showHours"
                    class="p-3 rounded-lg min-w-20 sm:p-4"
                    :style="unitBackgroundStyle"
                >
                    <div class="text-3xl font-bold sm:text-4xl">
                        {{ timeLeft.hours }}
                    </div>
                    <div class="text-xs sm:text-sm">Hours</div>
                </div>
                <!-- Minutes -->
                <div
                    v-if="props.showMinutes"
                    class="p-3 rounded-lg min-w-20 sm:p-4"
                    :style="unitBackgroundStyle"
                >
                    <div class="text-3xl font-bold sm:text-4xl">
                        {{ timeLeft.minutes }}
                    </div>
                    <div class="text-xs sm:text-sm">Minutes</div>
                </div>
                <!-- Seconds -->
                <div
                    v-if="props.showSeconds"
                    class="p-3 rounded-lg min-w-20 sm:p-4"
                    :style="unitBackgroundStyle"
                >
                    <div class="text-3xl font-bold sm:text-4xl">
                        {{ timeLeft.seconds }}
                    </div>
                    <div class="text-xs sm:text-sm">Seconds</div>
                </div>
            </div>
            <div v-else class="text-2xl font-semibold text-center md:text-3xl">
                {{ props.finishedText }}
            </div>
            <!-- Customizable Button -->
            <div
                v-if="
                    props.buttonEnabled &&
                    props.buttonText &&
                    props.buttonLink &&
                    !isCountdownFinished
                "
                class="flex justify-center mt-6"
            >
                <BlockButton
                    :text="props.buttonText"
                    :href="props.buttonLink"
                    :color="computedButtonBackgroundColor"
                    :textColor="computedButtonTextColor"
                    variant="primary"
                    class="px-5 py-2.5 text-base font-bold md:px-6 md:py-3"
                />
            </div>
        </div>
    </BlockContainer>
</template>
