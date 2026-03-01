<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

final class DeleteBlogAction
{
    public function execute(Blog $blog): void
    {
        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        $blog->topics()->detach();
        $blog->delete();
    }
}
