<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Cache::flush();
    }

    protected function signIn($user = null)
    {
        $user = $user ?? User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        return $user;
    }
}
