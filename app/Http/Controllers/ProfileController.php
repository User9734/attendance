<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profiles = Profile::all();
        foreach ($profiles as $key => $profile) {
            $sup = User::find($profile->tier);
            $profile->superviseur = $sup;
        }
        return response()->json([
            'status' => 'true',
            'data' => $profiles
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
            'name' => 'required|string',
            'tier' => 'required|integer',
            'extern_clock' => 'required|boolean',
        ]);
        $profile = new Profile();
        $profile->name = $request->name;
        $profile->tier = $request->tier;
        $profile->extern_clock = $request->extern_clock;
        $profile->save();
        if ($profile != null) {
            return response()->json([
                'status' => 'true',
                'data' => $profile
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
        $profile = Profile::find($id);
        $sup = User::find($profile->tier);
        $profile->superviseur = $sup;
        if ($profile != null) {
            return response()->json([
                'status' => 'true',
                'data' => $profile
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
            'name' => 'required|string',
            'tier' => 'required|integer',
            'extern_clock' => 'required|boolean',
        ]);
        $profile = Profile::find($id);
        $profile->name = $request->name;
        $profile->tier = $request->tier;
        $profile->extern_clock = $request->extern_clock;
        $profile->save();
        if ($profile->wasChanged()) {
            return response()->json([
                'status' => 'true',
                'data' => $profile
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
        $profile = Profile::find($id);
        if ($profile->has('user')) {
            return response()->json([
                'status' => 'false',
                'message' => 'Ce profil ne peut être supprimé car est lié à des utilisateurs.',
            ]);
        } else {
            $profile->delete();
            if ($profile->deleted_at != null) {
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
}
