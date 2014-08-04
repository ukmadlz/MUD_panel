<?PHP

class PlayerController extends BaseController {

	public $lang = array();

	public function __construct() {

		//-- Room Text
		$this->lang['room'][] = "Your in a dark room.";
		$this->lang['room'][] = "You appear to be in a corridor.";
		$this->lang['room'][] = "Your in a small clearing.";
		$this->lang['room'][] = "There is a small apple tree in this clearing.";
		$this->lang['room'][] = "Your walking through thick bush.";
		$this->lang['room'][] = "You are in a built up area.";
		$this->lang['room'][] = "There doesn't appear to be much around here.";
		$this->lang['room'][] = "You can hear the seagulls by the ocean.";
		$this->lang['room'][] = "Its getting near nightfall as you wander through.";

		//-- Monster Fight
		$this->lang['monster']['win'][] = "You fight the monster, and after a tough battle you deal the final blow.";
		$this->lang['monster']['win'][] = "You slay the beast with nothing but your hands and a hair-comb.";
		$this->lang['monster']['win'][] = "You fashion a weapon with what is around you, and slay the beast.";
		$this->lang['monster']['win'][] = "In a fit of rage you batter the beast.";
		$this->lang['monster']['win'][] = "Both you and the beast trade blows - but being the hero you are, you prevail.";
		$this->lang['monster']['win'][] = "You take an arrow to the knee - but you stil prevail.";

		//-- Monster Lose
		$this->lang['monster']['lose'][] = "The beast is just too strong - it overpowers you. You re-awaken in a room.";
		$this->lang['monster']['lose'][] = "The beast kills you, after you take an arrow to the knee. You start again in a room.";
		$this->lang['monster']['lose'][] = "In one swipe of the beast's claws your life is over. You re-awaken in a new dungeon.";

		//-- Picked up Loot
		$this->lang['loot'][] = "You have picked up";
		$this->lang['loot'][] = "You open the chest, and you pick up";
		$this->lang['loot'][] = "In the chest is a mystical weapon... ";
	}

	public function checkCoord()
	{
		//-- IDS
		$game_id = Route::input('game');
		$coord = Route::input('coord');
		$player_id = Route::input('pname');
		$level = Route::input('level');

		//-- Grab Map
		$map = Map::find($game_id);
		$map_array = json_decode($map->map, true);

		//-- Check if the co-ordinate is cool
		$coord = explode(",", $coord);
		$extra = array();
		$display = "";
		$complete = false;
		$boss = false;

		//-- $this->sendToPusher($game_id, $coord[0]."x".$coord[1], 'Start');

		if (!isset($map_array[$coord[0]."x".$coord[1]]))
		{
			//-- Room is actually a wall!
			$display = "You cannot move in that direction.";
			$status = false;

		} else {

			switch ($map_array[$coord[0]."x".$coord[1]]) {

				default:
					//-- Nothing found
					$status = false;
					$display = "Jake Error";
					break;

				case "#":
					//-- Floor, can user move in other directions?
					$status = true;
					$extra = $this->userMove($game_id, array('y' => $coord[1], 'x' => $coord[0]));
					$g = array();
					foreach($extra AS $n => $d) {
						if ($d == true) { $g[] = $n; }
					}
					$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id));
					//-- $display = "Your in a room. You can move ".implode(",", $g);
					$display = $this->lang['room'][rand(0, 2)]." You can move ".implode(",", $g);
					break;

				case "0":
					//-- Room is actually a wall!
					$display = "You cannot move in that direction. Try another.";
					$status = false;
					break;

				case "^":
					//-- Boss fight
					$status = true;
					$extra = $this->userMove($game_id, array('y' => $coord[1], 'x' => $coord[0]));
					$g = array();
					foreach($extra AS $n => $d) {
						if ($d == true) { $g[] = $n; }
					}
					$monster = $this->createMonster($player_id, $coord[0]."x".$coord[1], $level, $game_id);
					$mon = Monster::find($monster);
					$display = "You come across a Level ".$mon->level." ".$mon->monster." [ Your Level ".$level." ]. Fight or ".implode(",", $g);
					$complete = false;
					$boss = true;
					break;

				case "S":
					//-- back at spawn
					$status = true;
					$extra = $this->userMove($game_id, array('y' => $coord[1], 'x' => $coord[0]));
					$g = array();
					foreach($extra AS $n => $d) {
						if ($d == true) { $g[] = $n; }
					}
					$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id));
					$display = "Your back at the start! You can move ".implode(",", $g);
					break;

				case "M":
					//-- Fight Monster
					$status = true;
					$extra = $this->userMove($game_id, array('y' => $coord[1], 'x' => $coord[0]));
					$g = array();
					foreach($extra AS $n => $d) {
						if ($d == true) { $g[] = $n; }
					}
					$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id));
					$monster = $this->createMonster($player_id, $coord[0]."x".$coord[1], $level, $game_id);
					$mon = Monster::find($monster);
					$display = "You come across a Level ".$mon->level." ".$mon->monster." [ Your Level ".$level." ]. Fight or ".implode(",", $g);
					break;

				case "L":
					//-- Loot
					$status = true;
					$extra = $this->userMove($game_id, array('y' => $coord[1], 'x' => $coord[0]));
					$g = array();
					foreach($extra AS $n => $d) {
						if ($d == true) { $g[] = $n; }
					}
					$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id));
					$display = "You come across a chest! Open or ".implode(",", $g);
					break;
			}

		}

		return Response::json(array('status' => $status, 'extra' => $extra, "string" => $display, 'coord' => $coord[0]."x".$coord[1], 'complete' => $complete, 'boss' => $boss));
	}

	public function userMove($game_id, $current = array())
	{
		//-- Grab Map
		$map = Map::find($game_id);
		$map_array = json_decode($map->map, true);

		//-- Repsonce
		$resp = array("N" => false, "S" => false, "E" => false, "W" => false);

		//-- $current is our current co-ordinates...
		//-- Lets look up (Y - 1);
		//-- echo "I am: ".($current['x'])."x".$current['y']."\n";
		//-- echo "N Checking: ".($current['x'])."x".($current['y'] - 1)." - ".$map_array[$current['x']."x".($current['y'] - 1)]."\n";

		if (($current['y'] - 1 <= 0) || ($map_array[$current['x']."x".($current['y'] - 1)] == "0")) {
			$resp['N'] = false;
		} else {
			$resp['N'] = true;
		}

		//-- Check Down (Y + 1)
		//-- echo "S Checking: ".($current['x'])."x".($current['y'] + 1)." - ".$map_array[($current['x'])."x".($current['y'] + 1)]."\n";
		if (($current['y'] + 1 > $map->size) || ($map_array[($current['x'])."x".($current['y'] + 1)] == "0")) {
			$resp['S'] = false;
		} else {
			$resp['S'] = true;
		}

		//-- Check Left (X - 1)
		//-- echo "W Checking: ".($current['x'] - 1)."x".($current['y'])." - ".$map_array[($current['x'] - 1)."x".($current['y'])]."\n";
		if (($current['x'] - 1 <= 0) || ($map_array[($current['x'] - 1)."x".($current['y'])] == "0")) {
			$resp['W'] = false;
		} else {
			$resp['W'] = true;
		}

		//-- Check Right (X + 1)
		//-- echo "E Checking: ".($current['x'] + 1)."x".($current['y'])." -  ".$map_array[($current['x'] + 1)."x".($current['y'])]."\n";
		if (($current['x'] + 1 > $map->size) || ($map_array[($current['x'] + 1)."x".($current['y'])] == "0")) {
			$resp['E'] = false;
		} else {
			$resp['E'] = true;
		}

		return $resp;
	}

	public function sendToPusher($game_id, $coord, $channel = 'move')
	{
		$pusher = new Pusher(
			(($pusherKey = getenv("PUSHER_KEY"))?$pusherKey:''),
			(($pusherSecret = getenv("PUSHER_SECRET"))?$pusherSecret:''),
			(($pusherAppId = getenv("PUSHER_APP_ID"))?$pusherAppId:''),
			false, '
			https://api.pusherapp.com',
			443
		);

		if (strpos($coord['pid'], "witter") > 0) {
			$icon = "fa-twitter";
		} elseif (strpos($coord['pid'], "wilio") > 0) {
			$icon = "fa-phone";
		} else {
			$icon = "fa-gamepad";
		}
		$coord['icon'] = $icon;
		$pusher->trigger( 'map_' . $game_id, $channel, $coord );
	}

	public function createMonster($player, $coord, $level, $game)
	{
		//-- Get Monster from Mike
		$response = cURL::get('http://aqueous-springs-3113.herokuapp.com/monster?level=' . $level);
		$mike = json_decode($response, true);

		//-- Create Monster
		$monster = new Monster();
		$monster->player = $player;
		$monster->coord = $coord."x0";
		$monster->monster = $mike['name'];
		$monster->game_id = $game;
		$monster->level = $mike['overalLevel'];
		//- $monster->level = 1;
		$monster->save();

		//-- Return ID
		return $monster->id;
	}

	public function grabLoot()
	{
		//-- IDS
		$game_id = Route::input('game');
		$coord = Route::input('coord');
		$player_id = Route::input('pname');
		$level = Route::input('level');
		$coord = explode(",", $coord);

		//-- Grab Map
		$map = Map::find($game_id);
		$map_array = json_decode($map->map, true);

		//-- Mike
		$response = cURL::get('http://aqueous-springs-3113.herokuapp.com/loot');
		$mike = json_decode($response, true);

		//-- Make sure Coord is Loot
		switch ($map_array[$coord[0]."x".$coord[1]]) {

			default:
				//-- Nothing found
				$status = false;
				$display = "No Loot";
				return Response::json(array('status' => false, 'string' => "There is nothing to open."));
				break;

			case "L":
				//-- Awesome - send Loot as an object
				$status = true;

				//-- Remove loot from system
				$map_array[$coord[0]."x".$coord[1]] = "#";
				$map->map = json_encode($map_array);
				$map->save();

				$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id, 'loot' => $mike['name']." (".$mike['modifier'].")"), 'loot');

				return Response::json(array('status' => $status, 'loot' => $mike, 'string' => $this->lang['loot'][rand(0, 2)]." ".$mike['name']." (".$mike['modifier'].")"));
				break;
		}

	}

	public function fightMonster()
	{
		//-- IDS
		$game_id = Route::input('game');
		$coord = Route::input('coord');
		$player_id = Route::input('pname');
		$level = Route::input('level');
		$coord = explode(",", $coord);

		//-- Grab Map
		$map = Map::find($game_id);
		$map_array = json_decode($map->map, true);

		//-- Grab Monster
		$monster = Monster::where('player', $player_id)->where('coord', $coord[0]."x".$coord[1]."x0")->where('game_id', $game_id)->first();
		//-- dd($player_id." ".$coord[0]."x".$coord[1]."x0 ".$game_id);

		//-- Check if the co-ordinate is cool
		$extra = array();
		$display = "";
		$complete = false;
		$mike = array();
		$skip = false;

		//-- Ask mike who wins
		try {
			$response = cURL::get('http://aqueous-springs-3113.herokuapp.com/fight?player={%22overalLevel%22:'.$level.'}&monster={%22overalLevel%22:'.$monster->level.'}');
			$mike = json_decode($response, true);
		} catch (Exception $e) {
			$mike['success'] = true;
			$skip = true;
		}

		$g = rand(0, 100);
		if ($g < 90) {
			$mike['success'] = true;
		} else {
			$mike['success'] = false;
		}

		if ($mike['success'] == false) {
			$status = "dead";
			$display = $this->lang['monster']['lose'][rand(0, 2)];
			$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id, "your_l" => $level, "there_l" => $monster->level, 'data' => $g), 'dead');
		} else {
			$status = true;
			$display = $this->lang['monster']['win'][rand(0, 6)];
			$this->sendToPusher($game_id, array("coord" => $coord[0]."x".$coord[1], "pid" => $player_id, 'data' => $g), 'beat');
		}

		if ($skip != true) {
			$monster->delete();
		}

		return Response::json(array('status' => $status, "string" => $display, 'coord' => $coord[0]."x".$coord[1]));
	}

	public function language()
	{

	}
}
