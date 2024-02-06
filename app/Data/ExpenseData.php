<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class ExpenseData extends Data
{
    public function __construct(
        public int|Optional $id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'd-m-Y')]
        public Carbon $date,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $amount,
        public ?string $notes,
        #[MapName('is_regular')]
        public bool $isRegular,
        public CategoryData $category,
        public ?ProjectData $project,
        #[MapName('created_at')]
        public Carbon $createdAt,
        public UserData $user,
    ) {
    }
}
