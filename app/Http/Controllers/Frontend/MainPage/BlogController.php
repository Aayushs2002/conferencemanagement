<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class BlogController extends Controller
{
    private const CACHE_TTL = 3600; 
    private const RELEVANT_BLOGS_LIMIT = 3;

    public function __construct(
        private readonly Blog $blog
    ) {}

    public function index(): View
    {
        try {
            $blogs = $this->getActiveBlogs();
            return view('frontend.main-page.blog.index', compact('blogs'));
        } catch (Throwable $e) {
            Log::error('Error fetching blogs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('frontend.main-page.blog.index', ['blogs' => collect()]);
        }
    }

    public function singlePage(Blog $blog): View
    {
        try {
            $relevantBlogs = $this->getRelevantBlogs($blog->id);
            return view('frontend.main-page.blog.single-page', compact('blog', 'relevantBlogs'));
        } catch (Throwable $e) {
            Log::error('Error fetching single blog page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'blog_id' => $blog->id
            ]);

            return view('frontend.main-page.blog.single-page', [
                'blog' => $blog,
                'relevantBlogs' => collect()
            ]);
        }
    }

    private function getActiveBlogs()
    {
        return Cache::remember('blogs.active', self::CACHE_TTL, function () {
            return $this->blog->whereStatus(1)->get();
        });
    }

    private function getRelevantBlogs(int $excludeBlogId)
    {
        $cacheKey = "blogs.relevant.{$excludeBlogId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($excludeBlogId) {
            return $this->blog
                ->where('id', '!=', $excludeBlogId)
                ->where('status', 1) 
                ->latest()
                ->take(self::RELEVANT_BLOGS_LIMIT)
                ->get();
        });
    }
}
