<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\User\Society;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function __construct(
        private readonly Society $society
    ) {}

    public function index()
    {
        $societies =
            $this->society
            ->where('status', 1)
            ->with(['users' => function ($query) {
                $query->select('users.id', 'users.f_name');
            }])
            ->get();

        return view('frontend.main-page.about-us.index', compact('societies'));
    }
}
