<?php

namespace App\Http\Controllers;

use App\Actions\Blogs\CreateBlogAction;
use App\Actions\Blogs\DeleteBlogAction;
use App\Actions\Blogs\TogglePublishAction;
use App\Actions\Blogs\UpdateBlogAction;
use App\Actions\Blogs\UploadBlogImageAction;
use App\Http\Requests\Blogs\StoreBlogRequest;
use App\Http\Requests\Blogs\UpdateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $blogs = $request->user()
            ->blogs()
            ->with('topics')
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => $request->user()->blogs()->count(),
            'published' => $request->user()->blogs()->published()->count(),
            'draft' => $request->user()->blogs()->draft()->count(),
        ];

        return Inertia::render('blogs/Index', [
            'blogs' => $blogs,
            'stats' => $stats,
        ]);
    }

    public function create(Request $request): Response
    {
        $topics = $request->user()->topics()->orderBy('name')->get();

        return Inertia::render('blogs/Create', [
            'topics' => $topics,
        ]);
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $blog = (new CreateBlogAction)->execute(
            user: $request->user(),
            data: $request->validated()
        );

        return redirect()->route('blogs.index')
            ->with('success', 'Blog created successfully.');
    }

    public function edit(Request $request, Blog $blog): Response
    {
        $this->authorize('update', $blog);

        $topics = $request->user()->topics()->orderBy('name')->get();

        $blog->load('topics');

        return Inertia::render('blogs/Edit', [
            'blog' => array_merge($blog->toArray(), [
                'cover_image_url' => $blog->cover_image_url,
            ]),
            'topics' => $topics,
        ]);
    }

    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        $this->authorize('update', $blog);

        (new UpdateBlogAction)->execute(blog: $blog, data: $request->validated());

        return redirect()->route('blogs.index')
            ->with('success', 'Blog updated successfully.');
    }

    public function destroy(Request $request, Blog $blog): RedirectResponse
    {
        $this->authorize('delete', $blog);

        (new DeleteBlogAction)->execute(blog: $blog);

        return redirect()->route('blogs.index')
            ->with('success', 'Blog deleted.');
    }

    public function togglePublish(Request $request, Blog $blog): RedirectResponse
    {
        $this->authorize('update', $blog);

        (new TogglePublishAction)->execute(blog: $blog);

        return back()->with('success', 'Blog status updated.');
    }

    public function uploadImage(Request $request, Blog $blog): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $blog);

        $request->validate(['image' => ['required', 'image', 'max:4096']]);

        $url = (new UploadBlogImageAction)->execute(file: $request->file('image'));

        return response()->json(['url' => $url]);
    }
}
