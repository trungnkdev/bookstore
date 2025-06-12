<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class CheckoutController extends Controller
{
  public function index(Request $request)
  {
    return Inertia::render('checkout/index');
  }
}