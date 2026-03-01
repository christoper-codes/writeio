<?php

namespace App\Actions\Blogs;

use App\Models\Blog;

final class UpdateBlogAction
{
    public function execute(Blog $blog, array $data): Blog
    {
        $slug = (new GenerateBlogSlugAction)->execute(
            user: $blog->user,
            title: $data['title'],
            exclude_id: $blog->id
        );

        $cover_image = $blog->cover_image;
        if (isset($data['cover_image'])) {
            $cover_image = (new UploadCoverImageAction)->execute(
                file: $data['cover_image'],
                old_path: $blog->cover_image
            );
        }

        $was_draft = $blog->status === 'draft';
        $now_published = ($data['status'] ?? $blog->status) === 'published';

        $blog->update([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'cover_image' => $cover_image,
            'status' => $data['status'] ?? $blog->status,
            'published_at' => ($was_draft && $now_published) ? now() : $blog->published_at,
        ]);

        if (array_key_exists('topic_ids', $data)) {
            $blog->topics()->sync($data['topic_ids'] ?? []);
        }

        return $blog->fresh();
    }
}
