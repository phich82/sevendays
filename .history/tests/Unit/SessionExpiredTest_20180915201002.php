<?php

namespace Tests\Unit;

use Mockery;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Auth\ThrottlesLogins;
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
     * Test show the error message when login failed (invalid credentials)
     *
     * Condition:
     * - Credentials is invalid
     * Expectation:
     * - See text 'These credentials do not match our records.'
     */
    public function test1ShowErrorMessageWhenLoginFailed()
    {
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'xxx'])
             ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * Test show the error message when attempted login over 5 times with invalid credentials
     *
     * Condition:
     * - Enter credentials with invalid values
     * - Repeat login for 5 times
     * - 6th login with valid credentals
     * Expectation:
     * - 'delay' less than or equals to 60 (seconds)
     * - See text 'Too many login attempts. Please try again in {delay} seconds.'
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
        $errorMsg = session('errors')->all()[0]; // session('errors') = session()->get('errors')
        $this->assertEquals(1, preg_match('/\d+/', $errorMsg, $matches));

        $maxDelay = 60; // 1 minute
        $delay    = (int)$matches[0];
        $this->assertLessThanOrEqual($maxDelay, $delay);
        $response->assertSessionHasErrors(['email' => 'Too many login attempts. Please try again in '.$delay.' seconds.']);
    }

    public function test4AllowLoginAfterDelayTime()
    {
        // try login for 5 times with invalid credentials
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'xxx'.$i])
                 ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        }

        // 6th login with valid credentials
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'secret'])
             ->assertSessionHasErrors('email');

        // mock for over delay time
        $m = Mockery::mock(ThrottlesLogins::class)->shouldAllowMockingProtectedMethods();
        $m->shouldReceive('hasTooManyLoginAttempts')->andReturn(false);
        $this->app->instance(ThrottlesLogins::class, $m);

        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'secret'])
             ->assertSessionHasErrors('email');
    }

    public function test5RedirectToLoginPageWhenSessionExpired()
    {
        //
    }

    /**
     * Test login successfully
     *
     * Condition:
     * - Enter credentials with valid values
     * - Click on button 'Login'
     * Expectation:
     * - Redirect to Home page (/home)
     * - See text 'You are logged in!'
     *
     * @return void
     */
    public function test6LoginSuccessfully()
    {
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'secret'])
             ->assertRedirect(route('home'))
             ->followRedirects()
             ->assertSee('You are logged in!');
    }

    /**
     * prepare data for test
     *
     * @return void
     */
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
