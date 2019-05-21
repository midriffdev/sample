<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Game;
use App\Store;
use App\Promo;
use App\Competition;

use Auth;
use Session;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function schedule()
    {
    	$dateBegin = Carbon::now()->setTime(0,0);
		$dateEnd = Carbon::now()->setTime(23,59);

    	if (Auth::check()) {
            $user = User::find(Auth::user()->id);
        } else {
            $user = false;
        }

        if (Session::has('sesUserStoreId')) {
            $store = Store::find(Session::get('sesUserStoreId'));
        } else {
            $store = false;
        }
            

        $scheduleGames = Game::where('date_time','>',$dateBegin)
                                ->where('date_time','<',$dateEnd)
                                ->get();

        $scheduleGames->load(['competition','homeTeam','awayTeam']);
        $competitions = Competition::where('status',true)->get();

        //$store = Store::find($user->store_id);

    	return response()->json([
    		'user'		=> $user,
    		'store'		=> $store,
    		'date'		=> Carbon::now()->format('l d F'),
    		'schedule'	=> $scheduleGames,
            'competitions'   => $competitions
    	]);
    }

    public function updateSchedule(Request $request)
    {
        $newDateBegin = Carbon::parse($request->input('newDate'))->setTime(0,0,0);
        $newDateEnd = Carbon::parse($request->input('newDate'))->setTime(23,59,59);
        $scheduleGames = Game::where('date_time','>',$newDateBegin)->where('date_time','<',$newDateEnd)->get();
        $scheduleGames->load(['competition','homeTeam','awayTeam']);

        return response()->json($scheduleGames);
    }
}
