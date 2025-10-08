<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Cms\Feature;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function index()
    {
        $features = Feature::whereStatus(1)->get();
        return view('frontend.main-page.solution.index', compact('features'));
    }
}
