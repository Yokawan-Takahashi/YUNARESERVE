<?php

namespace App\Http\Controllers;

class LpController extends Controller
{
    public function index()
    {
        return view('lp.index');
    }

    public function terms()
    {
        return view('lp.terms');
    }

    public function privacy()
    {
        return view('lp.privacy');
    }
}
