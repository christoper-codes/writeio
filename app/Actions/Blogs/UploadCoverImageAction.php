<?php

namespace App\Actions\Blogs;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UploadCoverImageAction
{
    public function execute(UploadedFile $file, ?string $old_path = null): string
    {
        if ($old_path) {
            Storage::disk('public')->delete($old_path);
        }

        return $file->store('covers', 'public');
    }
}
