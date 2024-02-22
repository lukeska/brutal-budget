<?php

namespace App\Data;

use App\Models\Project;
use App\Rules\MaxProjectsPerTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Momentum\Lock\Data\DataResource;
use Spatie\LaravelData\Optional;

class ProjectData extends DataResource
{
    protected string $modelClass = Project::class;

    public function __construct(
        public int|Optional $id,
        public string $name,
        public string $hex,
    ) {
    }

    public static function rules(): array
    {
        $rules = [
            'name' => [
                'max:50',
                'required',
                new MaxProjectsPerTeam(),
            ],
            'hex' => [
                'required',
                'size:7',
            ],
        ];

        if (Route::current()->parameter('project')) {
            // If the 'category' route parameter is present, add the ignore rule
            $rules['name'][] = Rule::unique('projects', 'name')
                ->ignore(Route::current()->parameter('project'))
                ->where('team_id', Auth::user()->currentTeam->id);
        } else {
            // If creating a new record, add the unique rule with the additional condition
            $rules['name'][] = Rule::unique('projects', 'name')
                ->where('team_id', Auth::user()->currentTeam->id);
        }

        return $rules;
    }
}
