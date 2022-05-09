<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use App\Models\Justification;
use Illuminate\Http\Request;
use Location;

class JustificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ipaddress = '';
       if (isset($_SERVER['HTTP_CLIENT_IP']))
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_X_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       else if(isset($_SERVER['REMOTE_ADDR']))
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       else
           $ipaddress = 'UNKNOWN';  
        
        $datas = Location::get($ipaddress);
    
       return $datas;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $PublicIP = $request->ip;
        $datas = Location::get($PublicIP);
        
        return $datas;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $justif = Justification::with('clock')->where('clock_id',$id)->first();
        $user = $request->user('api');
        $justif->cause = $request->cause;
        $justif->state = $request->state;
        $justif->clock_id = $user->id;
        $justif->save();
        if ($justif->wasChanged()) {
            return response()->json([
                'status' => 'true',
                'message' => 'justified',
                'data' => $justif
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'not justified'
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
        //
    }
}
