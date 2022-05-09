<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => 'true',
            'data' => User::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string|min:10',
            'salary' => 'required|integer',
            'profile_id' => 'required|integer',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->salary = $request->salary;
        $user->profile_id = $request->profile_id;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        if ($user != null) {
            return response()->json([
                'status' => 'true',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'not stored.',
            ]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user != null) {
            return response()->json([
                'status' => 'true',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 'true',
                'message' => 'not found.',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string|min:10',
            'salary' => 'required|integer',
            'profile_id' => 'required|integer',
            'email' => 'required|email|unique',
            'password' => 'required|string|min:6',
        ]);
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->salary = $request->salary;
        $user->profile_id = $request->profile_id;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        if ($user != null) {
            return response()->json([
                'status' => 'true',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'not updated.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        if ($user->deleted_at != null) {
            return response()->json([
                'status' => 'true',
                'message' => 'deleted',
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'not deleted',
            ]);
        }
        
    }
}
