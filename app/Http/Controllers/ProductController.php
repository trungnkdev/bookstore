<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $direction = $request->input('sort', 'asc');
            $query->orderBy('price', $direction);
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        $products = $query->paginate(10)->appends($request->all());

        return Inertia::render('Product', [
            'products' => $products,
            'filters' => $request->only('search', 'sort', 'direction'),
            'categories' => Category::select('id', 'name')->get()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return Inertia::render('ProductDetail', [
            'product' => $product->load('category'),
            'relatedProducts' => Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get()
        ]);
    }

    public function search(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('keyword')) {
            $query->where('name', 'like', "%$search%");
        }

        $products = $query->paginate(12)->appends($request->all());

        return Inertia::render('ProductSearch', [
            'keyword' => $search,
            'products' => $products,
        ]);
    }
    
}
