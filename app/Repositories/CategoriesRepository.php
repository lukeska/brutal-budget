<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoriesRepository
{
    public function getAll(int $teamId): Collection
    {
        $key = "categories-getAll-{$teamId}";
        $tags = $this->getCacheTags($teamId);

        if (Cache::tags($tags)->has($key)) {
            $categories = Cache::tags($tags)->get($key);
        } else {
            $categories = Category::query()
                ->where('team_id', $teamId)
                ->orderBy('name')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $categories);
        }

        return $categories;
    }

    public function getCacheTags(int $teamId): array
    {
        return ["categories-{$teamId}"];
    }

    public function flushCache(int $teamId): void
    {
        Cache::tags($this->getCacheTags($teamId))->flush();
    }
}
