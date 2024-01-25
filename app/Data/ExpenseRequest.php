<?php

namespace App\Data;

use App\Rules\MaxExpensePerMonth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class ExpenseRequest extends Data
{
    public function __construct(
        public int|Optional $id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date,
        public int $amount,
        public ?string $notes,
        #[MapName('is_regular')]
        public bool $isRegular,
        #[MapName('category_id')]
        public int $categoryId,
        #[MapName('project_id')]
        public ?int $projectId,
        public int $months = 1,
    ) {
    }

    public static function rules(): array
    {
        return [
            'amount' => [
                'required',
                'integer',
                'gt:0',
            ],
            'date' => ['required', 'date', 'after:last year', 'before:+2 year', new MaxExpensePerMonth(Auth::user()->currentTeam->id)],
            'notes' => ['nullable'],
            'category_id' => ['required', 'exists:App\Models\Category,id'], // TODO: scope to categories for the current group
            'project_id' => ['sometimes', 'nullable', 'exists:App\Models\Project,id'], // TODO: scope to projects for the current group
            'is_regular' => ['sometimes', 'bool'],
            'months' => ['sometimes', 'integer', 'gt:0'],
        ];
    }

    public static function messages(): array
    {
        return [
            'date.after' => 'The date cannot be more than 1 year in the past.',
            'date.before' => 'The date cannot be more than 2 years in the future.',
        ];
    }
}
