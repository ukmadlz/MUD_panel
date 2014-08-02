<?PHP

class PlayerController extends BaseController {
	
	public function checkCoord()
	{
		//-- IDS
		$game_id = Route::input('game');
		$coord = Route::input('coord');
		
		//-- Grab Map
		$map = Map::find($game_id);
		$map_array = json_decode($map->map, true);
		
		//-- Check if the co-ordinate is cool
		$coord = explode(",", $coord);
		$extra = array();
		$display = "";
		
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
				$this->sendToPusher($game_id, $coord[0]."x".$coord[1]);
				$display = "Your in a room. You can move ".implode(",", $g);
				break;
			
			case "0":
				//-- Room is actually a wall!
				$display = "You cannot move in that direction.";
				$status = false;
				break;
			
			case "^":
				//-- Stairs
				$status = true;
				$display = "Dungeon Complete";
				break;
		}
		
		return Response::json(array('status' => $status, 'tile' => $map_array[$coord[0]."x".$coord[1]], 'extra' => $extra, "string" => $display));
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
		if (($current['y'] + 1 >= $map->size) || ($map_array[($current['x'])."x".($current['y'] + 1)] == "0")) {
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
		if (($current['x'] + 1 >= $map->size) || ($map_array[($current['x'] + 1)."x".($current['y'])] == "0")) {
			$resp['E'] = false;
		} else {
			$resp['E'] = true;
		}
		
		return $resp;
	}
	
		
	public function sendToPusher($game_id, $coord)
	{
		$pusher = new Pusher( "56e8164e0555e60345c9", "3dc8ae413981c7ae30f0", "83979", false, 'https://api.pusherapp.com', 443 );
		$pusher->trigger( 'map_' . $game_id, 'move', $coord );
	}
}