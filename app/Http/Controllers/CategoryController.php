<?php

namespace App\Http\Controllers;

use App\Data\CategoryData;
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
            'categories' => CategoryRequest::collection($this->categoriesRepository->getAll(Auth::user()->current_team_id)),
            'totals' => $this->monthlyTotalsRepository->getCategoriesTotals(Auth::user()->current_team_id),
        ]);
    }

    public function create()
    {
        if (Request::user()->cannot('create', Category::class)) {
            return redirect(route('categories.index'))->withErrors([
                'limit' => 'You reach the limit of categories this team can have.',
            ]);
        }

        try {
            $category = CategoryRequest::validate(Request::all());
        } catch (ValidationException $e) {
            return redirect(route('categories.index'))->withErrors($e->errors());
        }

        Auth::user()->currentTeam->categories()->create($category);

        Request::session()->flash('message', 'Category created correctly');
        Request::session()->flash('category', CategoryData::from($category));

        return redirect(route('categories.index'));
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
