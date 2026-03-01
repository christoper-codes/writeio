<?php

namespace App\Actions\Blogs;

use Illuminate\Http\UploadedFile;

final class UploadBlogImageAction
{
    public function execute(UploadedFile $file): string
    {
        $path = $file->store('blog-images', 'public');

        return asset('storage/'.$path);
    }
}
