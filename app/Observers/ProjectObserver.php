<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectsRepository;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    public function __construct(protected ProjectsRepository $projectsRepository)
    {
    }

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        /*
         * update onboarding status
         */
        // TODO: look into this. Not sure using Auth::user() in an observer is a good idea
        //Auth::user()->onboardingStatusProjectCreated?->complete();

        $this->projectsRepository->flushCache($project->team_id);
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        $this->projectsRepository->flushCache($project->team_id);
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        $this->projectsRepository->flushCache($project->team_id);
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
