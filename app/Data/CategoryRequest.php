<?php

namespace App\Data;

use App\Models\Category;
use App\Rules\MaxCategoriesPerTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Momentum\Lock\Data\DataResource;
use Spatie\LaravelData\Optional;

class CategoryRequest extends DataResource
{
    protected string $modelClass = Category::class;

    public function __construct(
        public int|Optional $id,
        public string $name,
        public string $icon,
        public string $hex,
    ) {
    }

    public static function rules(): array
    {
        $rules = [
            'name' => [
                'max:50',
                'required',
                new MaxCategoriesPerTeam(),
            ],
            'icon' => ['required'],
            'hex' => [
                'required',
                'size:7',
            ],
        ];

        if (Route::current()->parameter('category')) {
            // If the 'category' route parameter is present, add the ignore rule
            $rules['name'][] = Rule::unique('categories', 'name')
                ->ignore(Route::current()->parameter('category'))
                ->where('team_id', Auth::user()->currentTeam->id);
        } else {
            // If creating a new record, add the unique rule with the additional condition
            $rules['name'][] = Rule::unique('categories', 'name')
                ->where('team_id', Auth::user()->currentTeam->id);
        }

        return $rules;
    }
}
