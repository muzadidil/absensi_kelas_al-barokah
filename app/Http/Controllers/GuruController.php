<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruController extends Controller
{
     public function index()
    {
        // Later: fetch guru data here
        return view('guru.dashboard');
    }
}
