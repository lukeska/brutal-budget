<?php

namespace App\Http\Controllers;

use App\Data\CategoryRequest;
use App\Models\Category;
use App\Repositories\CategoriesRepository;
use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoriesRepository $categoriesRepository,
        protected MonthlyTotalsRepository $monthlyTotalsRepository,
    ) {
    }

    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => CategoryRequest::collection($this->categoriesRepository->getAll(Auth::user()->currentTeam->id)),
            'totals' => $this->monthlyTotalsRepository->getCategoriesTotals(Auth::user()->currentTeam->id),
            'canCreate' => Request::user()->can('create', Category::class),
        ]);
    }

    public function create()
    {
        if (Request::user()->cannot('create', Category::class)) {
            abort(403);
        }

        try {
            $category = CategoryRequest::validate(Request::all());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $category = Auth::user()->currentTeam->categories()->create($category);

        Request::session()->flash('message', 'Category created correctly');
        Request::session()->flash('category', CategoryRequest::from($category));

        return back();
    }

    public function update(Category $category, CategoryRequest $data)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $category->update($data->all());

        Request::session()->flash('message', 'Category updated correctly');

        return redirect(route('categories.index'));
    }

    public function updateIcon(Category $category, CategoryRequest $data)
    {
        if (Auth::user()->cannot('update', $category)) {
            abort(403);
        }

        $category->update($data->include('icon', 'hex')->toArray());

        Request::session()->flash('message', 'Category updated correctly');

        return redirect(route('categories.index'));
    }

    public function delete(Category $category)
    {
        if (Auth::user()->cannot('delete', $category)) {
            abort(403);
        }

        $category->delete();

        Request::session()->flash('message', 'Category deleted correctly');

        return redirect(route('categories.index'));
    }
}
