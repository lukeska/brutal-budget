<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Auth::user()
                ->currentTeam
                ->categories()
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function create()
    {
        $attributes = Request::validate([
            'name' => ['required',
                'max:50',
                Rule::unique('categories', 'name')
                    ->where('team_id', Auth::user()
                    ->currentTeam->id)
            ],
            'icon' => ['required'],
            'hex' => ['required', 'size:7'],
        ]);

        Auth::user()->currentTeam->categories()->create($attributes);

        Request::session()->flash('message', 'Category created correctly');

        return redirect('/categories');
    }

    public function updateHex(Category $category)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $attributes = Request::validate([
            'hex' => ['required', 'size:7'],
        ]);

        $category->update($attributes);

        Request::session()->flash('message', 'Category updated correctly');

        return redirect('/categories');
    }

    public function update(Category $category)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $attributes = Request::validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('categories', 'name')
                    ->ignore($category->id)
                    ->where('team_id', Auth::user()->currentTeam->id)
            ],
        ]);

        $category->update($attributes);

        Request::session()->flash('message', 'Category updated correctly');

        return redirect('/categories');
    }

    public function updateIcon(Category $category)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $attributes = Request::validate([
            'icon' => ['required'],
        ]);

        $category->update($attributes);

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
