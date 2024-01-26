<?php

namespace App\Observers;

use App\Models\Project;
use App\Repositories\ProjectsRepository;

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
