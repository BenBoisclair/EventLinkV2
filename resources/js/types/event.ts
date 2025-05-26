export interface EventType {
    id: number;
    name: string;
    slug?: string;
    description?: string;
    start_date?: string;
    end_date?: string;
    location?: string;
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
