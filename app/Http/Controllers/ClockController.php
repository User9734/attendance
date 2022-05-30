<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use App\Models\Justification;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Stevebauman\Location\Facades\Location;
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
        $clockjs = Clock::has('justif')->with('user')->with('justif')->get();
        return response()->json([
            'status' => 'true',
            'data' => $clocks,
            'nb_pts' => count($clocks),
            'nb_abs' => count($clockjs),
            'nb_prs' => count($clocks) - count($clockjs)
        ]);
    }

    public function indexbyday(Request $request)
    {
        $clocks = Clock::with('user')->with('justif')->get();
        $clocks->groupBy('created_at');
        return response()->json([
            'status' => 'true',
            'data' => $clocks,
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
        $sup = Profile::find($user->profile_id);
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
            
            if ($clock != null) {
                if ($clock->hour_start > $time) {
                    $justif = new Justification();
                    if (intval(explode(':', $clock->hour_start)[1]) > 30) {
                        $justif->late_hours = strval(date_parse($clock->hour_start)['hour'] - intval(explode(':', $time)[0])) + 1;
                        $clock->latehours = strval(date_parse($clock->hour_start)['hour'] - intval(explode(':', $time)[0])) + 1;
                        $clock->save();
                    }
                    else {
                        $clock->latehours = strval(date_parse($clock->hour_start)['hour'] - intval(explode(':', $time)[0]));
                        $clock->save();
                        $justif->late_hours = strval(date_parse($clock->hour_start)['hour'] - intval(explode(':', $time)[0]));
                    }
                    $justif->cause = '';
                    $justif->state = 'processing';
                    $justif->clock_id = $clock->id;
                    $justif->sup_id = $sup->tier;
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
        $clocks = Clock::with('user','justif')->find($id);
        return response()->json([
            'status' => 'true',
            'data' => $clocks
        ]);
    }

    public function getPerformances(Request $request)
    {
            $perfs = Clock::with('justif')->get();
            $perfs = $perfs->groupBy('user_id');
        foreach ($perfs as $key => $perf) {
            $number = 0;
            $int = 0;
            $abs = 0; 
            $total_heures_presences = 0;
            foreach ($perf as $key => $el) {
                global $total_heures_presences;
                $int++;
                if ($el->has('justif')) {
                    $abs++;
                }
                if ($el->hour_start < $el->hour_end) {
                    $a = new Carbon($el->hour_end);
                    $b = new Carbon($el->hour_start);
                    $d = $a->diffInMinutes($b) / 60;
                    if ((($d - intval($d)) * 60) > 30) {
                        $total_heures_presences += (intval($d) + 1);
                    } else {
                        $total_heures_presences += intval($d);
                    }
                } else {
                    $total_heures_presences += 0;
                }
                
            } 
            for ($i=0; $i < $int; $i++) { 
                $number += $perf[strval($i)]['latehours'];
            }
            $perf['nb_absences'] = $abs;
            $perf['total_heures_absences'] = $number;
            $perf['total_heures_presences'] = $total_heures_presences;
            $perf['author'] = User::with('profile')->find($perf['0']['user_id']);
        } 
        $perfs->sortByDesc('nb_absences'); 
        return response()->json([
            'status' => 'true',
            'data' => $perfs
        ]);
    }  
    
    public function getRecapitulatifs(Request $request)
    {
        if ($request->from != null && $request->to != null) {
            $perfs = Clock::with('justif', 'user')
            ->where('created_at', '>', $request->from)
            ->where('created_at', '<', $request->to)
            ->get();
            return response()->json([
                'status' => 'true',
                'data' => $perfs,
            ]);
        }
        else if ($request->month != null) {
            $perfs = Clock::with('justif', 'user')
            ->where('created_at', 'like', $request->month.'-%')
            ->get();
            return response()->json([
                'status' => 'true',
                'data' => $perfs,
            ]);
        }
        else if ($request->day != null) {
            $perfs = Clock::with('justif', 'user')
            ->where('created_at', 'like', $request->day.'%')
            ->get();
            return response()->json([
                'status' => 'true',
                'data' => $perfs,
            ]);
        }
        else if ($request->year != null) {
            $perfs = Clock::with('justif', 'user')
            ->where('created_at', 'like', $request->year.'-%-%')
            ->get();
            return response()->json([
                'status' => 'true',
                'data' => $perfs,
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
