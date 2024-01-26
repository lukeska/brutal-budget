<?php

namespace App\Http\Middleware;

use App\Repositories\CategoriesRepository;
use App\Repositories\ProjectsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        $categoriesRepository = new CategoriesRepository();
        $projectsRepository = new ProjectsRepository();
        $user = Auth::user();

        return array_merge(parent::share($request), [
            'categories' => fn () => $user ? $categoriesRepository->getAll($user->current_team_id) : null,
            'projects' => fn () => $user ? $projectsRepository->getAll($user->current_team_id) : null,
        ]);
    }
}
