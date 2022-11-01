<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test register user and login
     *
     * @return void
     */
    public function test_register_and_login()
    {
        $response = $this->post('/api/register', [
            'name' => 'Jon',
            'email' => 'jon@snow.com', 
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);
        $response->assertStatus(201);

        $response = $this->post('/api/login', ['email' => 'jon@snow.com', 'password' => 'secret']);

        $response->assertStatus(201)->assertJson(function(AssertableJson $json) {
            $json->hasAll(['user', 'token']);
        });

        $user = \App\Models\User::where('email', 'jon@snow.com')->first();
        
        if($user) {
            $user->delete();
        }

        $this->assertTrue(true);
    }
}
