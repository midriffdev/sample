<?php

namespace App\Http\Controllers\Admin;

use App\Store;
use App\Test;
use App\Game;
use DB;

use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dateNow = Carbon::now()->setTime(0,0,0);
        $dateNextWeek = Carbon::now()->addDays(3);

        $games = Game::where('date_time','>=',$dateNow)->where('date_time','<',$dateNextWeek)->get();

        return view('admin.stores.index')
                    ->with('stores',Store::all())
                    ->with('nextGames',$games);
    }

    public function setBigGames(Request $request, $storeId)
    {
        $store = Store::find($storeId);
        $store->bigGames()->sync($request->input('biggames'));

        return response()->json($store);
    }

    public function setNotBroadcasted(Request $request, $storeId)
    {
        $store = Store::find($storeId);
        $store->notBroadcasted()->sync($request->input('notbroadcasted'));

        return response()->json($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = Store::insert($request->except('_token'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        $dateNow = Carbon::now()->setTime(0,0,0);
        $dateNextWeek = Carbon::now()->addDays(3);

        // $games = Game::where('date','>=',$dateNow)->where('date','<',$dateNextWeek)->get();
        $games = Game::where('date_time','>=',$dateNow)->get();

        return view('admin.stores.show')
                    ->with('store',$store)
                    ->with('nextGames',$games);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }
	
	public function test()
	{
		$this->nfl_league();
		$this->nba_league();
		$this->mlb_league();
		$this->nhl_league();
		$this->cfb_league();
	}
	
	private function nfl_league()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/nfl/scores/json/Schedules/2019",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: e52da963c797464c9433b1147a0b06f5",
				"postman-token: 27feafb3-ac54-becf-310f-1752a986dd1f"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$nfl_data =  json_decode($response, TRUE);
			if(!empty($nfl_data)){
				$league_data = array();
				$i = 0;
				foreach($nfl_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GlobalGameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GlobalGameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['Date'];
						$league_data[$i]['away_team_id'] 	= $val['GlobalAwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['GlobalHomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
	
	private function nba_league()
	{
		$curl = curl_init();
		$year = date('Y');
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/nba/scores/json/Games/".$year,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: 71fe209ac4a743cfa9b528076c5d6ad0"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$nba_data =  json_decode($response, TRUE);
			if(!empty($nba_data)){
				$league_data = array();
				$i = 0;
				foreach($nba_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['DateTime'];
						$league_data[$i]['away_team_id'] 	= $val['AwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['HomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
	
	public function mlb_league()
	{
		$curl = curl_init();
		$year = date('Y');
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/mlb/scores/json/Games/".$year,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: 33d3d3c982024c308d7087f449818787"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$nba_data =  json_decode($response, TRUE);
			if(!empty($nba_data)){
				$league_data = array();
				$i = 0;
				foreach($nba_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['DateTime'];
						$league_data[$i]['away_team_id'] 	= $val['AwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['HomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
	
	public function nhl_league()
	{
		$curl = curl_init();
		$year = date('Y');
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/nhl/scores/json/Games/".$year,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: 04f60f35ea314387a449437a4e874267"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$nhl_data =  json_decode($response, TRUE);
			if(!empty($nhl_data)){
				$league_data = array();
				$i = 0;
				foreach($nhl_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['DateTime'];
						$league_data[$i]['away_team_id'] 	= $val['AwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['HomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
	
	public function cfb_league()
	{
		$curl = curl_init();
		$year = date('Y');
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/cfb/scores/json/Games/".$year,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: f41f248b38fd45948de0414708766414"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$cfb_data =  json_decode($response, TRUE);
			if(!empty($cfb_data)){
				$league_data = array();
				$i = 0;
				foreach($cfb_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['DateTime'];
						$league_data[$i]['away_team_id'] 	= $val['AwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['HomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
	
	public function cbb_league()
	{
		$curl = curl_init();
		$year = date('Y');
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sportsdata.io/v3/cbb/scores/json/Games/".$year,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"ocp-apim-subscription-key: 447ad0bca5784e2b911003009aebff5c"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$cbb_data =  json_decode($response, TRUE);
			if(!empty($cbb_data)){
				$league_data = array();
				$i = 0;
				foreach($cbb_data as $val){
					
					$count = DB::table('tests')->where('gameId', $val['GameID'])->count();
					
					if($count > 0){
						//update if any changes
					}else{
						$league_data[$i]['gameId'] 			= $val['GameID'];
						$league_data[$i]['home_team_name'] 	= $val['HomeTeam'];
						$league_data[$i]['away_team_name'] 	= $val['AwayTeam'];
						$league_data[$i]['date_time'] 		= $val['DateTime'];
						$league_data[$i]['away_team_id'] 	= $val['AwayTeamID'];
						$league_data[$i]['home_team_id'] 	= $val['HomeTeamID'];
						$i++;
					}
				}
				Test::insert($league_data);
				return true;
			}else{
				return 'no match found.';
			}
		}
	}
}
