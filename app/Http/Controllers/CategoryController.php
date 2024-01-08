<?php

namespace App\Http\Controllers;

use App\Data\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Auth::user()
            ->currentTeam
            ->categories()
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => CategoryRequest::collection($categories),
        ]);
    }

    public function create()
    {
        $category = CategoryRequest::validate(Request::all());

        Auth::user()->currentTeam->categories()->create($category);

        Request::session()->flash('message', 'Category created correctly');

        return redirect('/categories');
    }

    public function update(Category $category, CategoryRequest $data)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $category->update($data->all());

        Request::session()->flash('message', 'Category updated correctly');

        return redirect('/categories');
    }

    public function updateIcon(Category $category, CategoryRequest $data)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $category->update($data->include('icon', 'hex')->toArray());

        Request::session()->flash('message', 'Category updated correctly');

        return redirect('/categories');
    }

    public function delete(Category $category)
    {
        if (Auth::user()->cannot('delete', $category)) {
            abort(403);
        }

        $category->delete();

        Request::session()->flash('message', 'Category deleted correctly');

        return redirect('/categories');
    }
}
