<?php

namespace App\Actions\Topics;

use App\Models\Topic;

final class DeleteTopicAction
{
    public function execute(Topic $topic): void
    {
        $topic->blogs()->detach();
        $topic->delete();
    }
}
