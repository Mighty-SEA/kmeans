<?php

namespace App\Http\Controllers\Documentation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index()
    {
        return view('Documentation.index');
    }

    public function model()
    {
        return view('Documentation.model');
    }

    public function view()
    {
        return view('Documentation.view');
    }

    public function controller()
    {
        return view('Documentation.controller');
    }

    public function route()
    {
        return view('Documentation.route');
    }

    public function middleware()
    {
        return view('Documentation.middleware');
    }

    public function migration()
    {
        return view('Documentation.migration');
    }
} 