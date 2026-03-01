<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $topics = $user->topics()
            ->with(['blogs' => function ($query) {
                $query->published()->latest('published_at')->limit(6);
            }])
            ->withCount(['blogs' => function ($query) {
                $query->published();
            }])
            ->having('blogs_count', '>', 0)
            ->get();

        $recent_blogs = $user->blogs()
            ->published()
            ->with('topics')
            ->latest('published_at')
            ->limit(6)
            ->get();

        return Inertia::render('profile/Show', [
            'profile_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'blogs_count' => $user->blogs()->published()->count(),
            ],
            'topics' => $topics,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    public function blog(string $username, string $slug): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $blog = $user->blogs()
            ->published()
            ->where('slug', $slug)
            ->with('topics')
            ->firstOrFail();

        $other_blogs = $user->blogs()
            ->published()
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('profile/BlogShow', [
            'profile_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
            ],
            'blog' => $blog,
            'other_blogs' => $other_blogs,
        ]);
    }
}
