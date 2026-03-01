export type BlogStatus = 'draft' | 'published';

export type Topic = {
    id: number;
    user_id: number;
    name: string;
    slug: string;
    created_at: string;
    updated_at: string;
    blogs?: Blog[];
    blogs_count?: number;
};

export type Blog = {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string | null;
    cover_image: string | null;
    cover_image_url: string | null;
    status: BlogStatus;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    topics?: Topic[];
    user?: import('./auth').User;
};

export type BlogStats = {
    total: number;
    published: number;
    draft: number;
};

export type PublicUser = {
    id: number;
    name: string;
    username: string;
    bio: string | null;
    blogs_count?: number;
};
