<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\StatusMapping;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('id','<>', auth()->user()->id)->get();
        $unit= "K";
        return view('home', compact('users','distance', 'unit'));
    }
    public function user_like(Request $request)
    {
        $id = $request->id;
        $check_another_like = StatusMapping::where('receiver_id', auth()->user()->id)->where('sender_id', $id)->first();
        $check_double_insert = StatusMapping::where('receiver_id', $id)->where('sender_id', auth()->user()->id)->get();
        // dd(isset($check_another_like));
        if(isset($check_another_like)){
            $check_another_like->update(['current_status'=>1]);
            return redirect()->back()->with('match', 'Its a match!' );
        }else{
            if(count($check_double_insert)>0){
                return redirect()->back()->with('status', 'You was already liked this user.');
            }else{
                StatusMapping::create(['receiver_id'=>$id, 'sender_id'=>auth()->user()->id, 'current_status'=>0]);
            }
        }
        
        return redirect()->back()->with('status', 'Liked Successfully');
    }
    public function user_dislike(Request $request)
    {
        return redirect()->back()->with('status', 'Disliked Successfully');
    }
}
