<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CategoryController extends Controller
{
    public function getCategories()
    {
        // Mengambil kategori secara distinct
        $categories = Course::select('category')->distinct()->get();

        return response()->json($categories);
    }
}
