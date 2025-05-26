import { defineStore } from "pinia";
import { ref } from "vue";
import { EventType } from "@/types/event";

export const useEventStore = defineStore("event", () => {
    const events = ref<EventType[]>([]);
    const currentEvent = ref<EventType | null>(null);
    function setEvents(initialEvents: EventType[]) {
        events.value = initialEvents;
    }

    function setCurrentEvent(event: EventType | null) {
        currentEvent.value = event;
    }

    function updateCurrentEventDetails(updatedDetails: {
        name: string;
        description?: string;
    }) {
        if (currentEvent.value) {
            currentEvent.value = {
                ...currentEvent.value,
                name: updatedDetails.name,
                description:
                    updatedDetails.description ??
                    currentEvent.value.description,
            };
            const index = events.value.findIndex(
                (e) => e.id === currentEvent.value?.id
            );
            if (index !== -1) {
                events.value[index] = {
                    ...events.value[index],
                    ...updatedDetails,
                };
            }
        }
    }

    return {
        events,
        currentEvent,
        setEvents,
        setCurrentEvent,
        updateCurrentEventDetails,
    };
});
