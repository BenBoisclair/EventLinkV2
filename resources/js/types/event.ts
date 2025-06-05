export interface EventType {
    id: number;
    team_id?: number;
    name: string;
    slug?: string;
    description?: string;
    start_date?: string;
    end_date?: string;
    location?: string;
    logo_path?: string | null;
    banner_path?: string | null;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
    registration_form?: {
        id: string;
        fields: Array<{
            name: string;
            label: string;
            type: string;
            required: boolean;
            enabled?: boolean;
        }>;
        is_active: boolean;
    };
}

export interface TeamEventData {
    event_count: number;
    event_limit: number;
    can_create_event: boolean;
    remaining_slots: number;
    has_unlimited_events: boolean;
    plan: {
        name: string;
        slug: string;
    } | null;
    billing_cycle: 'monthly' | 'annual';
}
