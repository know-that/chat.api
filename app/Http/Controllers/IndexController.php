<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        dd(app('request'));
    }
}
