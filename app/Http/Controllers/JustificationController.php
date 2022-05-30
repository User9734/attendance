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
       
    }

    public function getUnvalidated(Request $request)
    {
        $sup = $request->user('api');
       $justifs = Justification::with('clock')->where('sup_id', $sup->id)->where('state', 'justified')->get();
       return response()->json([
        'status' => 'true',
        'data' => $justifs
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
        
        if ($justif->wasChanged()) {
            $justif->state = 'justified';
            $justif->clock_id = $user->id;
            $justif->save();
            return response()->json([
                'status' => 'true',
                'data' => $justif
            ]);
        } else {
            $justif->state = 'processing';
            $justif->clock_id = $user->id;
            $justif->save();
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
