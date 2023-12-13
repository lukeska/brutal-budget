<?php

namespace App\Http\Controllers;

use App\Data\CategoryData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Projects/Index', [
            'projects' => null,
        ]);
    }
}
