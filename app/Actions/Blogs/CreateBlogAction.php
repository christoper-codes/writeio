<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use App\Models\User;

final class CreateBlogAction
{
    public function execute(User $user, array $data): Blog
    {
        $slug = (new GenerateBlogSlugAction)->execute(user: $user, title: $data['title']);

        $cover_image = null;
        if (isset($data['cover_image'])) {
            $cover_image = (new UploadCoverImageAction)->execute(file: $data['cover_image']);
        }

        $blog = $user->blogs()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'cover_image' => $cover_image,
            'status' => $data['status'] ?? 'draft',
            'published_at' => ($data['status'] ?? 'draft') === 'published' ? now() : null,
        ]);

        if (! empty($data['topic_ids'])) {
            $blog->topics()->sync($data['topic_ids']);
        }

        return $blog;
    }
}
