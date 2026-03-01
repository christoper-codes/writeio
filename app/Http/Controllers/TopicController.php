<?php

namespace App\Http\Controllers;

use App\Actions\Topics\CreateTopicAction;
use App\Actions\Topics\DeleteTopicAction;
use App\Http\Requests\Topics\StoreTopicRequest;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    public function index(Request $request): Response
    {
        $topics = $request->user()
            ->topics()
            ->withCount('blogs')
            ->orderBy('name')
            ->get();

        return Inertia::render('topics/Index', [
            'topics' => $topics,
        ]);
    }

    public function store(StoreTopicRequest $request): RedirectResponse
    {
        (new CreateTopicAction)->execute(
            user: $request->user(),
            name: $request->validated('name')
        );

        return back()->with('success', 'Topic created.');
    }

    public function destroy(Request $request, Topic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        (new DeleteTopicAction)->execute(topic: $topic);

        return back()->with('success', 'Topic deleted.');
    }
}
