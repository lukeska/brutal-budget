<?php

namespace Tests\Feature\Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class ApiTokenPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_permissions_can_be_updated(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');

            /** @phpstan-ignore-next-line  */
            return;
        }

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $token = $user->tokens()->create([
            'name' => 'Test Token',
            'token' => Str::random(40),
            'abilities' => ['create', 'read'],
        ]);

        /** @phpstan-ignore-next-line  */
        $response = $this->put('/user/api-tokens/'.$token->id, [
            /** @phpstan-ignore-next-line  */
            'name' => $token->name,
            'permissions' => [
                'delete',
                'missing-permission',
            ],
        ]);

        /** @phpstan-ignore-next-line  */
        $this->assertTrue($user->fresh()->tokens->first()->can('delete'));
        /** @phpstan-ignore-next-line  */
        $this->assertFalse($user->fresh()->tokens->first()->can('read'));
        /** @phpstan-ignore-next-line  */
        $this->assertFalse($user->fresh()->tokens->first()->can('missing-permission'));
    }
}
