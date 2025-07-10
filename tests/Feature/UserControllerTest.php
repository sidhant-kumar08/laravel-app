<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function test_user_registration(){

        $name = fake()->name();
        $email = fake()->email();
        $password = "password";

        $response = $this->post(route('register'), ['name' => $name, 'email' => $email, 'password' => $password]);

        $response->assertCreated();
    }


    public function test_user_login()
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token']);
    }

    public function test_user_login_with_invalid_email()
    {
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertJson(['message' => 'User not found']);
        $response->assertStatus(404);
    }

    public function test_user_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertJson(['message' => 'Incorrect credentials']);
        $response->assertStatus(401);
    }
}
