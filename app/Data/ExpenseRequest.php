<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
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
        #[MapName('category_id')]
        public int $categoryId,
    ) {
    }

    public static function rules(): array
    {
        return [
            'amount' => [
                'required',
                'comma_decimal_positive'
            ],
            'date' => ['required', 'date'],
            'notes' => ['nullable'],
            'category_id' => ['required','exists:App\Models\Category,id'] // TODO: scope to categories for the current group
        ];
    }
}
