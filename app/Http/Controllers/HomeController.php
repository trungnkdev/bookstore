<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class HomeController extends Controller
{
  public function index(Request $request)
  {
    return Inertia::render('Home', [
      'products' => Product::all()
    ]);
  }
}