<?php

/*
 * Co-ordinates are Y,X,Z
 *
 * I know thats fucked up. Fuck you.
*/

class MapController extends BaseController {

	//-- Variables
	public $size = 1;
	public $out = array('x' => 0, 'y' => 0);
	public $spawn = array('x' => 0, 'y' => 0);
	public $object = array('floor' => '#', 'wall' => '0', 'stairs' => '^', 'spawn' => 'S');
	
	//-- Output
	public $output;
	public $pathfind;
	public $walked;
	public $debug = false;
	
	//-- Create Map
	public function createMap($size = 8)
	{
		$this->size = $size;	
		
		$this->createBasicMap();
		$this->createSpawn();
		
		//-- Pathfinding
		$complete = $this->pathfinder();
		
		if ($complete == false) {
			$this->createMap();
		}
		
		if ($this->debug) {
			$this->basicStyle();
		} else {
			$id = $this->storeMap();
			return Response::json(array('map_id' => $id, 'starting' => array('x' => $this->spawn['y'], 'y' => $this->spawn['x'], 'z' => 0)));
			//--return Response::json(array('map' => json_encode($this->)));
		}

	}
	
	//-- Create Floor and Walls
	public function createBasicMap()
	{
		$map = array();
		for($y = 1;$y <= $this->size; $y++)
		{
			for($x = 1;$x <= $this->size; $x++)
			{
				//-- Pathfinding algorithm wants it in YxX format
				if (rand(0, 100) > 20) {
					$map[$y."x".$x]['weight'] = 1.0;
					$this->output[$y."x".$x] = $this->object['floor'];
				} else {
					$map[$y."x".$x]['weight'] = 100.0;
					$this->output[$y."x".$x] = $this->object['wall'];
				}
			}
		}	
		
		$this->pathfind = $map;
	}
	
	public function createSpawn()
	{
		//-- Create random number from grid
		$this->spawn['x'] = rand(1, $this->size - 1);
		$this->spawn['y'] = rand(1, $this->size - 1);
		$this->output[$this->spawn['y']."x".$this->spawn['x']] = $this->object['spawn'];
		
		$this->out['x'] = rand(1, $this->size - 1);
		$this->out['y'] = rand(1, $this->size - 1);
		$this->output[$this->out['y']."x".$this->out['x']] = $this->object['stairs'];
	}
	
	public function pathfinder()
	{
		$path = new PathFinder();
		$path->noDiagonalMovement(); //This supresses diagonal movement, only recommended for strict square maps
		$path->setOrigin($this->spawn['y'], $this->spawn['x']); //This is the origin coordinate, aka 1x1
		$path->setDestination($this->out['y'], $this->out['x']); //Destination coordinate
		$path->setMap($this->pathfind);
		$this->walked = $path->returnPath(); //returns an array with coordinates (like 1x2 etc)
		
		//-- Can I actually be saved?
		if ($this->walked[count($this->walked) - 1] == ($this->out['y']."x".$this->out['x'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function basicStyle()
	{
		$modifiedresult = array_flip($this->walked);
		echo '<table>';
		for($x=1;$x<=$this->size;$x++){
			echo '<tr>';
			for($y=1;$y<=$this->size;$y++){
				echo '<td style="width:16px;height:16px;text-align:center;vertical-align:middle;color:#FFF;';
				
				switch($this->output[$y."x".$x]) {
					case "#";
						echo 'background: #2ecc71;">';
						break;
					case "0";
						echo 'background: #2c3e50;">';
						break;
					case "^";
						echo 'background: #c0392b;">';
						break;
					case "S";
						echo 'background: #f1c40f;">';
						break;
				}				
				//marking it with an X if it was included in path or if it was starting position
				if(isset($modifiedresult[$y.'x'.$x])){
					echo 'x';
				} else {
					echo '&nbsp;';
				}
				
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';	
	}
	
	public function viewMap()
	{
		$map_id = Route::input('game');
		
		$map = Map::find($map_id);
		$map_array = json_decode($map->map, true);
		
		echo '<table>';
		for($x=1; $x<=$map->size; $x++){
			echo '<tr>';
			for($y=1; $y<=$map->size; $y++){
				echo '<td style="width:16px;height:16px;text-align:center;vertical-align:middle;color:#FFF;';
				
				switch($map_array[$y."x".$x]) {
					case "#";
						echo 'background: #2ecc71;">';
						break;
					case "0";
						echo 'background: #2c3e50;">';
						break;
					case "^";
						echo 'background: #c0392b;">';
						break;
					case "S";
						echo 'background: #f1c40f;">';
						break;
				}						
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		
		echo "<p>Array</p>";
		echo "<pre>";
		print_r($map_array);
		echo "</pre>";
	}
	
	public function storeMap()
	{
		$map = new Map();
		$map->map = (json_encode($this->output));
		$map->size = $this->size;
		$map->save();
		
		return $map->id;
	}

	
}