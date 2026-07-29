export type ProjectImage = {
    id: number;
    project_id: number;
    title: string;
    image: string;
    image_url: string;
    sort_order: number;
    created_at: string;
    updated_at: string;
};

export type Project = {
    id: number;
    title: string;
    slug: string;
    thumbnail: string | null;
    thumbnail_url: string | null;
    description: string;
    has_embedding: boolean;
    created_at: string;
    updated_at: string;
    images?: ProjectImage[];
    images_count?: number;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
};
