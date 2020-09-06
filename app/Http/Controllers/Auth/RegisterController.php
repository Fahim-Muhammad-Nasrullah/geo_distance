<?php

namespace App\Http\Controllers\Auth;
use Stevebauman\Location\Facades\Location;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\Console\Input\Input;


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
        $position = Location::get($ip);
        $latitude = $position->latitude;
        $longitude = $position->longitude;
        $country = $position->countryName;
        $zipCode = $position->zipCode;
        $areaCode = $position->areaCode;
        $countryCode = $position->countryCode;
        $regionCode = $position->regionCode;
        $regionName = $position->regionName;
        $cityName = $position->cityName;
        $location = array("latitude"=>$latitude,"longitude"=>$longitude,"country"=>$country, 'zipCode'=> $zipCode, 'areaCode'=>$areaCode, 'countryCode'=>$countryCode, 'regionCode' =>$regionCode, 'regionName'=>$regionName, 'cityName'=>$cityName);
        $this->validator($request->all(), $location)->validate();
        if($files = $request->file('profile_photo')){
            $destinationPath = 'public/users/photos/';
            $extention = $files->getClientOriginalExtension();
            $profileImage = date('Y-m-d').'-'.rand(100,999).'.'.$extention ;
            $request->profile_photo->storeAs($destinationPath, $profileImage);
            $profile_photo =  'photos/'.$profileImage;
            $request->merge([ 'image' => $profile_photo ]);
        }
        event(new Registered($user = $this->create($request->all(), $location)));

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
    protected function validator(array $data, $location)
    {
        return Validator::make($data, $location, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_photo' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'gender' => 'required',
            'dob' => 'required|date',
            ]);
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data, $location)
    {
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_photo' => $data['profile_photo'],
            'location' => $location['cityName'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'dob' => Carbon::parse($data['dob'])->format('Y-m-d'),
            'gender' => $data['gender'],
            'image' => $data['image'],
        ]);
    }
}
