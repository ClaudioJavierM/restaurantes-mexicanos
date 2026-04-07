<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * /blog — paginated list, published only, newest first.
     */
    public function index(Request $request): View
    {
        $category = $request->query('categoria');

        $query = BlogPost::published()->orderByDesc('published_at');

        if ($category) {
            $query->byCategory($category);
        }

        $posts    = $query->paginate(12)->withQueryString();
        $featured = BlogPost::published()->featured()->orderByDesc('published_at')->first();

        return view('blog.index', [
            'posts'      => $posts,
            'featured'   => $featured,
            'categories' => BlogPost::categories(),
            'current'    => $category,
        ]);
    }

    /**
     * /blog/{slug} — single post, increment view_count.
     */
    public function show(BlogPost $post): View
    {
        // Only show published posts (404 otherwise)
        abort_unless($post->is_published, 404);

        $post->increment('view_count');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category, fn ($q) => $q->byCategory($post->category))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', [
            'post'    => $post,
            'related' => $related,
        ]);
    }

    /**
     * /blog/categoria/{category} — filter by category.
     */
    public function category(string $category): View
    {
        $categories = BlogPost::categories();

        abort_unless(array_key_exists($category, $categories), 404);

        $posts = BlogPost::published()
            ->byCategory($category)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', [
            'posts'      => $posts,
            'featured'   => null,
            'categories' => $categories,
            'current'    => $category,
        ]);
    }
}
