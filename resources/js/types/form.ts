export interface FormField {
    name: string;
    label: string;
    type: string;
    required: boolean;
    enabled?: boolean;
    deletable?: boolean;
    options?: string[];
}
