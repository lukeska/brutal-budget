<?php

namespace Tests\Feature\Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class CreateApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_tokens_can_be_created(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');

            /** @phpstan-ignore-next-line  */
            return;
        }

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->post('/user/api-tokens', [
            'name' => 'Test Token',
            'permissions' => [
                'read',
                'update',
            ],
        ]);

        $this->assertCount(1, $user->fresh()->tokens);
        /** @phpstan-ignore-next-line  */
        $this->assertEquals('Test Token', $user->fresh()->tokens->first()->name);
        /** @phpstan-ignore-next-line  */
        $this->assertTrue($user->fresh()->tokens->first()->can('read'));
        /** @phpstan-ignore-next-line  */
        $this->assertFalse($user->fresh()->tokens->first()->can('delete'));
    }
}
