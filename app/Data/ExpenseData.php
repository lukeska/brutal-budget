<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use App\Models\Currency;
use App\Models\Expense;
use Carbon\Carbon;
use Momentum\Lock\Data\DataResource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class ExpenseData extends DataResource
{
    protected string $modelClass = Expense::class;

    public function __construct(
        public int|Optional $id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'd-m-Y')]
        public Carbon $date,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $amount,
        public CurrencyData $currency,
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
