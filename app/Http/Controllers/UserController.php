<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
        $users = User::with('profile, clocks')->get();
        return response()->json([
            'status' => 'true',
            'data' => $users
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

    public function getSalaries()
    {
        $users = User::with('clocks')->with('profile')->get();
        foreach ($users as $key => $user) {
            $heures = 0;
            foreach ($user->clocks as $key => $clock) {
                $a = new Carbon($clock->hour_end);
                $b = new Carbon($clock->hour_start);
                $d = $a->diffInMinutes($b) / 60;
                if ((($d - intval($d)) * 60) > 30) {
                    $heures += (intval($d) + 1);
                } else {
                    $heures += intval($d);
                }
            }
            $user->salaire_fin = ($user->salary * $heures) / 160;
        }
        return response()->json([
            'status' => 'true',
            'data' => $users,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('profile')->with('justifs')->with('clocks')->find($id);
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
                'message' => 'employé non modifié.',
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
