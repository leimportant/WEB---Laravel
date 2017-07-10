<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Log;
use DB;
use App\Models\Plant;


class ProfileController extends Controller
{

	public function __construct() {
 
        $this->middleware('auth');
 
    }

    public function view($user_id)
    {
       $user_id = Auth::user()->id;
       // $data = DB::table('profile')->where(['user_id'=>$user_id])->get();
        $data = Profile::find(Auth::id());

        return view('auth.profile', compact('data'));
    }

     public function edit($user_id)
    {
       $user_id = Auth::user()->id;
       // $data = DB::table('profile')->where(['user_id'=>$user_id])->get();
        $data = Profile::find(Auth::id());

         $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        return view('auth.edit-profile', compact('data') +  $relations);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'fullname' => 'required',
            'email' => 'required|email',
        ]);

        // DB::beginTransaction();
        // try {

            $user = User::find(Auth::id());

            $exist = DB::table('profile')->where(['id'=>$user->id])->get();


             if(count($exist) > 0) {
                  
                DB::table('profile')
                    ->where('id', $user->id)
                    ->limit(1) 
                    ->update(array('phone' => $request->phone, 'plant' => $request->plant, 'dept_id' => $request->dept_id, 'sub_dept' => $request->sub_dept, 'gender' =>$request->gender, 'photo' => 'avatar.png'));
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->limit(1) 
                    ->update(array('name' => $request->fullname, 'email' => $request->email));

              

                }
                else  {

                $data=array('id' => $user->id, 'phone' => $request->phone, 'plant' => $request->plant, 'dept_id' => $request->dept_id, 'sub_dept' => $request->sub_dept, 'gender' =>$request->gender, 'photo' => 'avatar.png');
                DB::table('profile')->insert($data);

                DB::table('users')
                    ->where('id', $user->id)
                    ->limit(1) 
                    ->update(array('name' => $request->fullname, 'email' => $request->email));
   

                }
                

        //      DB::commit();

        //  } catch (\Exception $e) {
        //     DB::rollback();
        //     return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        // }

        return redirect()->route('home');

    }


}
