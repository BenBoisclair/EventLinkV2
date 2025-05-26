export interface Attendee {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    company?: string;
    job_title?: string;
    custom_fields?: string | Record<string, any>;
    is_verified: boolean;
    created_at: string;
}
