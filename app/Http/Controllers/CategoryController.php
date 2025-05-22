<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($sort, $direction);
        }

        $categories = $query->paginate(2)->appends($request->all());

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search', 'sort', 'direction'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);
        Category::create($validated);

        return redirect()->back()->with('success', 'Category created');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);
        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        Category::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Selected items deleted.');
    }
}
