<?php

namespace App\Http\Controllers;

use App\Data\ExpenseData;
use App\Data\ProjectData;
use App\Models\Project;
use App\Repositories\ExpensesRepository;
use App\Repositories\ProjectsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectsRepository $projectsRepository,
        protected ExpensesRepository $expensesRepository)
    {
    }

    public function index()
    {
        return Inertia::render('Projects/Index', [
            'projects' => ProjectData::collection($this->projectsRepository->getAll(Auth::user()->currentTeam->id)),
            'totals' => $this->expensesRepository->getProjectsTotals(Auth::user()->currentTeam->id, Auth::user()->currency_id),
            'canCreate' => Request::user()->can('create', Project::class),
        ]);
    }

    public function show(Project $project)
    {
        $expenses = $this->expensesRepository->getByProject(Auth::user()->currentTeam->id, $project->id, Auth::user()->currency_id);

        $total = $expenses->sum('converted_amount');

        return Inertia::render('Projects/Show', [
            'project' => ProjectData::from($project),
            'expenses' => ExpenseData::collection($expenses),
            'total' => $total,
        ]);
    }

    public function create()
    {
        if (Request::user()->cannot('create', Project::class)) {
            abort(403);
        }

        try {
            $project = ProjectData::validate(Request::all());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        Auth::user()->currentTeam->projects()->create($project);

        Auth::user()->onboardingStatusProjectCreated?->complete();

        Request::session()->flash('message', 'Project created correctly');
        Request::session()->flash('category', ProjectData::from($project));

        return redirect('/projects');
    }

    public function update(Project $project, ProjectData $data)
    {
        if (Auth::user()->cannot('update', $project)) {
            abort(403);
        }

        $project->update($data->all());

        Request::session()->flash('message', 'Project updated correctly');

        return redirect('/projects');
    }

    public function updateHex(Project $project, ProjectData $data)
    {
        if (Auth::user()->cannot('update', $project)) {
            abort(403);
        }

        $project->update($data->include('hex')->toArray());

        Request::session()->flash('message', 'Project updated correctly');

        return redirect('/projects');
    }

    public function delete(Project $project)
    {
        if (Auth::user()->cannot('delete', $project)) {
            abort(403);
        }

        $project->delete();

        Request::session()->flash('message', 'Project deleted correctly');

        return redirect('/projects');
    }
}
