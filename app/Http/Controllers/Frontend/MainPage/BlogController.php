<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::whereStatus(1)->get();
        return view('frontend.main-page.blog.index', compact('blogs'));
    }

    public function singlePage(Blog $blog)
    {
        // dd($blog);
        $relevantBlogs = Blog::where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get();
        return view('frontend.main-page.blog.single-page', compact('blog', 'relevantBlogs'));
    }
}
