<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;

class ProjectTotalData extends Data
{
    public function __construct(
        #[MapName('project_id')]
        public int $projectId,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $total,
    ) {
    }
}
