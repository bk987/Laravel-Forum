<?php

namespace App\Http\Controllers;

use App\Category;
use App\Thread;

class CategoriesController extends Controller
{
    /**
     * Display all threads in the specified category.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $threads = Thread::where('category_id', $category->id)->paginate(20);

        return view('categories.show', compact('category', 'threads'));
    }
}
