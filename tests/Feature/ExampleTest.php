<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Change this from assertStatus(200) to assertStatus(302)
        // Because '/' now redirects to '/tasks'
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
