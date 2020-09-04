<?php

namespace App\Http\Controllers\Auth;
use Stevebauman\Location\Facades\Location;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;



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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $ip= $_SERVER['REMOTE_ADDR'];
        dd($position = Location::get($ip));
        // dd($request->all());
        // dd($request->hasfile('profile_photo'));  
        if($files = $request->file('profile_photo')){
            // File::delete('storage/users/'.$photo->image_url);
            $destinationPath = 'public/users/photos/';
            $extention = $files->getClientOriginalExtension();
            $profileImage = date('Y-m-d').'-'.rand(100,999).'.'.$extention ;
// dd($request->file('profile_photo'), $profileImage,$destinationPath, $profileImage);
            $request->profile_photo->storeAs($destinationPath, $profileImage);
            $request->profile_photo =  'photos/'.$profileImage;
            // File::delete('storage/users/photos/'.$identiy->user_id.'/'.$identiy->image_file);

        }
        // dd($request->profile_photo);
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new Response('', 201)
                    : redirect($this->redirectPath());
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            // dd($data['profile_photo']);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_photo' => $data['profile_photo'],
            // 'latitude' => 
            // 'logitude' =>
        ]);
    }
}
