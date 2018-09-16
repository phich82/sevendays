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
    public function testShowErrorMessageWhenLoginFailed()
    {
        $this->post(route('login'), ['email' => 'phich82@gmail.com', 'password' => 'xxx'])
             ->followRedirects()
             ->assertSee('You are logged in!');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
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
