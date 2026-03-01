<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Str;

final class GenerateBlogSlugAction
{
    public function execute(User $user, string $title, ?int $exclude_id = null): string
    {
        $base = Str::slug($title);

        $query = Blog::where('user_id', $user->id)->where('slug', $base);
        if ($exclude_id) {
            $query->where('id', '!=', $exclude_id);
        }

        if (! $query->exists()) {
            return $base;
        }

        $counter = 2;
        do {
            $candidate = $base.'-'.$counter;
            $q = Blog::where('user_id', $user->id)->where('slug', $candidate);
            if ($exclude_id) {
                $q->where('id', '!=', $exclude_id);
            }
            $counter++;
        } while ($q->exists());

        return $candidate;
    }
}
