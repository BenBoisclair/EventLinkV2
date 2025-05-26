export interface Exhibitor {
    id: number;
    name: string;
    contact_email: string;
    contact_phone?: string | null;
    description?: string | null;
    created_at: string;
    updated_at: string;
    logo_path?: string | null;
    banner_path?: string | null;
}

export interface ExhibitorPagination {
    data: Exhibitor[];
    total: number;
}
