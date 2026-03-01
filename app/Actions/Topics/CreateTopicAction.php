<?php

namespace App\Actions\Topics;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Str;

final class CreateTopicAction
{
    public function execute(User $user, string $name): Topic
    {
        $slug = Str::slug($name);

        return $user->topics()->create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}
