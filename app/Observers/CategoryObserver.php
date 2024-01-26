<?php

namespace App\Observers;

use App\Models\Category;
use App\Repositories\CategoriesRepository;

class CategoryObserver
{
    public function __construct(protected CategoriesRepository $categoriesRepository)
    {
    }


    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->categoriesRepository->flushCache($category->team_id);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->categoriesRepository->flushCache($category->team_id);
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->categoriesRepository->flushCache($category->team_id);
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
