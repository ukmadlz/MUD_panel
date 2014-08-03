<?PHP

class PagesController extends BaseController {

    protected $layout = 'template';

	public function showIndex()
    {
		$this->layout->content = View::make('index');
	}
	
	public function showMaps()
    {
		$maps = Map::orderBy('created_at', 'desc')->get();
		$this->layout->content = View::make('maps', array('maps' => $maps));
	}
	
	public function prettyView()
	{
		$map_id = Route::input('game');
		$html = "";
		
		$map = Map::find($map_id);
		$map_array = json_decode($map->map, true);
		
		$width = number_format(620 / $map->size, 0);
		
		$html .= '<table>';
		for($x=1; $x<=$map->size; $x++){
			$html .= '<tr>';
			for($y=1; $y<=$map->size; $y++){
				$html .= '<td style="width:'.$width.'px;height:'.$width.'px;text-align:center;vertical-align:middle;color:#FFF;opacity:1;';
				
				switch($map_array[$y."x".$x]) {
					case "#";
						$html .= 'background: #2ecc71;"';
						break;
					case "0";
						$html .= 'background: #2c3e50;"';
						break;
					case "^";
						$html .= 'background: #c0392b;"';
						break;
					case "S";
						$html .= 'background: #7f8c8d;"';
						break;
					case "M":
						$html .= 'background: #8e44ad;"';
						break;
					case "L":
						$html .= 'background: #f1c40f;"';
						break;
				}
				$html .= " class='pos_".$y."x".$x."'>";
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		
		$this->layout = View::make('presentation');
		$this->layout->content = View::make('single_map', array('html' => $html, 'game_id' => $map_id));
	}
	
	public function jsonView()
	{
		//-- 	
		$map_id = Route::input('game');
		$map = Map::find($map_id);
		$map_array = json_decode($map->map, true);
		$json = array();
		
		for($x=1; $x<=$map->size; $x++){
			for($y=1; $y<=$map->size; $y++){
				$json[$x][$y] = $map_array[$y."x".$x];
			}
		}
		
		return Response::json($json);
	}
	
	public function latestJson()
	{
		//--
		$map = Map::orderBy('id', 'desc')->first();
		$map_array = json_decode($map->map, true);
		$json = array();
		
		for($x=1; $x<=$map->size; $x++){
			for($y=1; $y<=$map->size; $y++){
				$json[$x][$y] = $map_array[$y."x".$x];
			}
		}
		
		return Response::json($json);
	}
	
	
	
}