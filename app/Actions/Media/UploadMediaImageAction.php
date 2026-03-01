<?php

namespace App\Actions\Media;

use Illuminate\Http\UploadedFile;

final class UploadMediaImageAction
{
    public function execute(UploadedFile $file): string
    {
        $path = $file->store('blog-images', 'public');

        return asset('storage/'.$path);
    }
}
