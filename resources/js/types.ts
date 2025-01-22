export interface Item {
    id: number;
    name: string;
    description: string | null;
    photo_path: string | null;
    box_id: number;
    created_at: string;
    updated_at: string;
}

export interface Box {
    id: number;
    name: string;
    description: string | null;
    location: string;
    photo_path: string | null;
    created_at: string;
    updated_at: string;
    items: Item[];
} 