<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $restaurants = Restaurant::approved()
            ->where('category_id', $category->id)
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'address', 'average_rating', 'total_reviews', 'description', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->paginate(24);

        return view('categories.show', compact('category', 'restaurants'));
    }
}
