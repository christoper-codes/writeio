<?php

namespace App\Actions\Blogs;

use App\Models\Blog;

final class TogglePublishAction
{
    public function execute(Blog $blog): Blog
    {
        if ($blog->status === 'published') {
            $blog->update(['status' => 'draft']);
        } else {
            $blog->update([
                'status' => 'published',
                'published_at' => $blog->published_at ?? now(),
            ]);
        }

        return $blog->fresh();
    }
}
