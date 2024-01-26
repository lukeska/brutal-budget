<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProjectsRepository
{
    public function getAll(int $teamId): Collection
    {
        $key = "projects-getAll-{$teamId}";
        $tags = $this->getCacheTags($teamId);

        if (Cache::tags($tags)->has($key)) {
            $projects = Cache::tags($tags)->get($key);
        } else {
            $projects = Project::query()
                ->where('team_id', $teamId)
                ->orderBy('name')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $projects);
        }

        return $projects;
    }

    public function getCacheTags(int $teamId): array
    {
        return ["projects-{$teamId}"];
    }

    public function flushCache(int $teamId): void
    {
        Cache::tags($this->getCacheTags($teamId))->flush();
    }
}
