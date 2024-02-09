<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Momentum\Lock\Data\DataResource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Optional;
use Illuminate\Support\Facades\Route;

class CategoryRequest extends DataResource
{
    protected string $modelClass = Category::class;

    public function __construct(
        public int|Optional $id,
        public string $name,
        public string $icon,
        public string $hex,
        #[MapName('monthly_totals_sum_amount')]
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public ?int $monthlyTotalsSumAmount,
    ) {
    }

    public static function rules(): array
    {
        $rules = [
            'name' => [
                'max:50',
                'required',
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
