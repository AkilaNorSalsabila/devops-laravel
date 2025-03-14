<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
{
return view('home');
}
public function about()
{
return view('about');
}
public function visidanmisi()
{
return view('visidanmisi');
}
public function alumni()
{
return view('alumni');
}
public function prestasi()
{
return view('prestasi');
}
}
