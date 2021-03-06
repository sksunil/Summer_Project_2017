<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use App\Address;
use Auth;

class YourAccountController extends Controller
{
	public function loginsecurity(Request $request){

		$users = Auth::user();

		$this->validate(request(),[
			'name' => 'required',
			'email' => 'required|email',
			'oldPassword' => 'required',
			'password' => 'required|confirmed'
			]);

		$currentPassword = $request->input('oldPassword');

		if (Hash::check($currentPassword, $users->password)){

			$request->user()->fill([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password)
				])->save();
			\Session::flash('loginsecurity.level', 'success');
			\Session::flash('loginsecurity.content','Profile Updated !!!');
			return back();
		}
		else{
			\Session::flash('loginsecurity.level', 'danger');
			\Session::flash('loginsecurity.content','Invalid old password !!!');
			return back();
		}
	}

	public function addresses(){
		$users = User::find(Auth::user()->id)->addresses;
		return view('Boot.addresses-show', compact('users'));
	}

	public function addressesadd(){
		// dd(request()->all());
		$this->validate(request(),[
			'user_id' => 'required',
			'pincode' => 'required|numeric|digits:6',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required'
			]);
		Address::create(request(['user_id','pincode','address','city','state']));
		\Session::flash('address.level', 'success');
		\Session::flash('address.content','Address added Successfully !!!');
		return back();
	}

	public function addressesupdate(Address $address){

		return view('Boot.addresses-edit',compact('address'));
	}

	public function addressesdelete(Address $address){

		$affectedRows = Address::destroy($address->id);
		if ($affectedRows == 1){
			\Session::flash('address.level', 'success');
			\Session::flash('address.content','Address deleted Successfully !!!');
			return back();
		}
		else{
			\Session::flash('address.level', 'danger');
			\Session::flash('address.content','Unable to delete Address !!!');
			return back();
		}
	}

	public function addressesupdateadd(Request $request){
		// dd(request()->all());
		$this->validate(request(),[
			'id' => 'required',
			'pincode' => 'required|numeric|digits:6',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required'
			]);

		$user = Address::find($request->id);
		$user->pincode = $request->pincode;
		$user->address = $request->address;
		$user->city = $request->city;
		$user->state = $request->state;
		$user->save();

		\Session::flash('address.level', 'success');
		\Session::flash('address.content','Address updated Successfully !!!');
		return back();
	}
}
