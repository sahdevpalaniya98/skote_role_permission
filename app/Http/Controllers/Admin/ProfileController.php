<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Validator;
use Session;
use Image;
use Auth;
use Hash;
use File;
use DB;

class ProfileController extends Controller{

    public function __construct(){
        //
    }

	public function showProfile(Request $request){
	    try {
	    	$data 				= [];
	    	$data['page_title'] = 'Profile';
	    	$data['breadcrumb'] = array(
	    	    array(
	    	        'link' 		=> route('admin.home'),
	    	        'title' 	=> 'Dashboard'
	    	    ),
	    	    array(
	    	        'title' 	=> 'Profile'
	    	    )
	    	);
            $data['user']               = Auth::user();
            // $data['devices']            = DB::table('sessions')->where('user_id', Auth::id())->get();
            $data['current_session_id'] = Session::getId();
	        return view('admin.profile.edit',$data);
	    } catch (\Exception $e) {
            dd($e);
            return abort(404);
	    }
	}

	public function profileUpdate(Request $request){
        try {
            $user           = Auth::user();
            if($user){
                $rules      = [
                    'name'              => 'required',
                    'email'             => 'required|string|email|max:255|unique:users,email,'.$user->id
                ];
                $messages   = [
                    'name.required'     => 'The fullname field is required.',
                    'email.required'    => 'The email field is required.'
                ];
                $validator  = Validator::make($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return redirect()->route('admin.profile')->withErrors($validator)->withInput();
                } else {
                    $user->name         = trim($request->name);
                    $user->email        = trim($request->email);
                    $user->updated_by   = Auth::id();
                    $user->updated_at   = date("Y-m-d H:i:s");
                    $user->save();
                    Session::flash('alert-message', 'Profile updated successfully.');
                    Session::flash('alert-class', 'success');
                    return redirect()->route('admin.profile');
                }
            }else{
                Session::flash('alert-message', 'User record not found.');
                Session::flash('alert-class', 'error');
                return redirect()->route('admin.profile');
            }
        } catch (\Exception $e) {
            Session::flash('alert-message', $e->getMessage());
            Session::flash('alert-class', 'error');
            return redirect()->route('admin.profile');
        }
	}

	public function showChangeForm(Request $request){
		try {
			$data                = [];
			$data['page_title']  = 'Account Setting';
			$data['breadcrumb']  = array(
			    array(
			        'link' 		    => route('admin.home'),
			        'title' 	    => 'Dashboard'
			    ),
			    array(
			        'title' 	    => 'Account Setting'
			    )
			);
		    return view('admin.profile.edit',$data);
		} catch (\Exception $e) {
			return abort(404);
		}
	}

	public function passwordUpdate(Request $request){
		try {
            $user           = Auth::user();
            if($user){
                $rules      = [
                    'current_password'                  => 'required|string|min:8',
                    'password'                          => 'required|string|min:8|confirmed',
                    'password_confirmation'             => 'required|string|min:8'
                ];
                $messages   = [
                    'current_password.required'         => 'The current password field is required.',
                    'password.required'                 => 'The new password field is required.',
                    'password_confirmation.required'    => 'The confirm password field is required.'
                ];
                $validator  = Validator::make($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return redirect()->route('admin.profile')
                        ->withErrors($validator)
                        ->withInput();
                } else {
                    if(Hash::check($request->current_password, Auth::user()->password)){
                        $user->password     = bcrypt($request->password);
                        $user->updated_by   = Auth::id();
                        $user->updated_at   = date("Y-m-d H:i:s");
                        $user->save();
                        Auth::logout();
                        Session::flash('alert-message', "Password changed successfully. Please login bellow with new password.");
                        Session::flash('alert-class', "success");
                        return back();
                    }else{
                        Session::flash('alert-message', 'Please enter correct current password.');
                        Session::flash('alert-class', 'error');
                        return redirect()->route('admin.profile');
                    }
                }
            }else{
                Session::flash('alert-message', 'User record not found.');
                Session::flash('alert-class', 'error');
                return redirect()->route('admin.profile');
            }
        } catch (\Exception $e) {
            Session::flash('alert-message', $e->getMessage());
            Session::flash('alert-class', 'error');
            return redirect()->route('admin.profile');
        }
	}

}
