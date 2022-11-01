<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Duplicate user test
     *
     * @return void
     */
    public function test_user_duplication() {

        $user1 = \App\Models\User::make([
            'name' => 'Jon',
            'email' => 'jondoe@test.com'
        ]);
        $user2 = \App\Models\User::make([
            'name' => 'Doe',
            'email' => 'jondoe2@test.com'
        ]);
        
        $this->assertTrue($user1->email != $user2->email);
    }

    /**
     * Register user with invalid password
     *
     * @return void
     */
    public function test_register_user_with_invalid_password() {
        $response = $this->post('/api/register', [
            'name' => 'Test',
            'email' => 'test@test.com', 
            'password' => 'test',
            'password_confirmation' => 'test'
        ]);
        $response->assertStatus(302);
    }

    /**
     * Check user does not exists in DB
     */

    public function test_user_not_added_to_db() {
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com'
        ]);
    }

    /**
     * Login invalid user
     *
     * @return void
     */
    public function test_login_invalid_user() {
        $response = $this->post('/api/login', ['email' => 'test@test.com', 'password' => 'test']);

        $response->assertStatus(401);
    }

    /**
     * Register user
     *
     * @return void
     */
    public function test_register_user() {
        $response = $this->post('/api/register', [
            'name' => 'Test',
            'email' => 'test@test.com', 
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);
        $response->assertStatus(201);
    }

    /**
     * Check user exists in DB
     */

    public function test_user_added_to_db() {
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com'
        ]);
    }

    /**
     * Login user
     *
     * @return void
     */
    public function test_login_user() {
        $response = $this->post('/api/login', ['email' => 'test@test.com', 'password' => 'secret']);

        $response->assertStatus(201);
    }

    /**
     * Delete user
     * 
     * @return void
     */

    public function test_delete_user() {
        $user = \App\Models\User::where('email', 'test@test.com')->first();
        
        if($user) {
            $user->delete();
        }

        $this->assertTrue(true);
    }
}
