<?php

namespace App\Data;

use Carbon\Carbon;
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
            'date' => ['required', 'date'],
            'notes' => ['nullable'],
            'category_id' => ['required', 'exists:App\Models\Category,id'], // TODO: scope to categories for the current group
            'project_id' => ['sometimes', 'nullable', 'exists:App\Models\Project,id'], // TODO: scope to projects for the current group
            'is_regular' => ['sometimes', 'bool'],
        ];
    }
}
