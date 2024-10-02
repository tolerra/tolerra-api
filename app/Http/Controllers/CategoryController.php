<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        // Mengambil kategori secara distinct
        $categories = Category::select('category')->distinct()->get();

        return response()->json($categories);
    }
}
