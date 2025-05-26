import axios from "axios";
import { route } from "ziggy-js";
import type { EventType } from "@/types/event";

export async function createEvent(
    name: string,
    description?: string,
    start_date?: string,
    end_date?: string,
    team_id?: number,
    user_id?: number
): Promise<EventType> {
    try {
        const res = await axios.post("/api/events", {
            name,
            description,
            start_date,
            end_date,
            team_id,
            user_id,
        });
        return res.data.event as EventType;
    } catch (error: any) {
        throw new Error(
            error?.response?.data?.message || "Failed to create event."
        );
    }
}
