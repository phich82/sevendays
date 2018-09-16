<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Jobs\SendMailJob;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
    * Handle a registration request for the application.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        $this->guard()->login($user);

        // create a job and delay 5 seconds
        $job = (new SendMailJob($user))->delay(Carbon::now()->addSeconds(5));
        // push job to queue
        dispatch($job);

        return view('auth.welcome');

        // run job: php artisan queue:work | php artisan queue:work --tries=3 | php artisan queue:work --timeout=60 | php artisan queue:work --sleep=3
        // restart job: php artisan queue:restart
        // monitoring queue:
        //  on linux: use 'supervisor'
        //      supervisorctl reread
        //      supervisorctl update
        //      supervisorctl start laravel-worker:*
        //  on windows: use 'forever'
        //      forever -c php artisan --queue:work --tries=3 --timeout=60 --sleep=5
    }
}
