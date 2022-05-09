<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use App\Models\Justification;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
class ClockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clocks = Clock::with('user')->with('justif')->get();
        return response()->json([
            'status' => 'true',
            'data' => $clocks
        ]);
    }

    /* public function getAbsencebyUser(Request $request)
    {
        $clocks = Clock::with('user')->has('justif')->get();
        return response()->json([
            'status' => 'true',
            'data' => $clocks
        ]);
    } */



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $time = "08:00:00";
        $user = $request->user('api');
        $old = Clock::where('user_id', $user->id)->where('created_at', 'like' , date('Y-m-d').'%')->first();
        if ($old != null) {
            $old->hour_end = date('H:i:s');
            $old->save();
            if ($old->wasChanged()) {
                return response()->json([
                    'status' => 'true',
                    'message' => 'hour end edited',
                    'data' => $old
                ]);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'hour end not edited'
                ]);
            }
            
        } else {
            $clock = new Clock();
            $clock->hour_start = date('H:i:s');
            $clock->hour_end = date('H:i:s');
            $clock->user_id = $user->id;
            $clock->save();
            if ($clock != null) {
                if ($clock->hour_start > $time) {
                    $justif = new Justification();
                    $justif->late_hours = strval(date_parse($clock->hour_start)['hour'] - intval(explode(':', $time)[0]));
                    $justif->cause = '';
                    $justif->state = 'processing';
                    $justif->clock_id = $clock->id;
                    $justif->save();
                }
                return response()->json([
                    'status' => 'true',
                    'data' => $clock
                ]);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'not stored'
                ]);
            }
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
        $clocks = Clock::find($id)->with('user')->with('justif')->get();
        return response()->json([
            'status' => 'true',
            'data' => $clocks
        ]);
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
        
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
