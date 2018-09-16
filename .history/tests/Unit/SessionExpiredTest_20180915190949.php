<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->startSession();

        if (!$this->user) {
            User::query()->delete();
            $this->prepareData();
        }
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test1ShowErrorMessageWhenLoginFailed()
    {
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'xxx'])
             ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * A basic test example.
     * Too many login attempts. Please try again in 39 seconds.
     *
     * @return void
     */
    public function test2ShowErrorMessageWhenAttemptedLoginOver5Times()
    {
        // try login for 5 times with invalid credentials
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'xxx'.$i])
                 ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        }

        // 6th login with valid credentials
        $response = $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'secret']);

        $response->assertSessionHasErrors('email');
        dd(session()->get('errors'));
        $errorMsg = session()->all()['errors']->all()[0];
        $this->assertEquals(1, preg_match('/\d+/', $errorMsg, $matches));

        $maxDelay = 60; // 1 minute
        $delay    = (int)$matches[0];
        $this->assertLessThanOrEqual($maxDelay, $delay);
        $response->assertSessionHasErrors(['email' => 'Too many login attempts. Please try again in '.$delay.' seconds.']);
    }

    /**
     * A basic test example.Too many login attempts. Please try again in 39 seconds.
     *
     * @return void
     */
    public function test3Login()
    {
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'secret'])
             ->followRedirects()
             ->assertSee('You are logged in!');
    }

    private function prepareData()
    {
        $this->user = factory(User::class)->create([
            'name' => 'Jhp Phich',
            'email' => 'phich82@gmail.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'remember_token' => str_random(10),
        ]);
    }
}
