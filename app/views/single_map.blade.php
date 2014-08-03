@section('main-area')

<div class="row">
	<div class="col-md-6">
		{{ $html }}
	</div>
	<div class="col-md-6">
		<iframe seamless="seamless" src="http://104.131.212.45/webface" width="100%" height="600px"/>
	</div>
</div>

@show 

@section('head-area')
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
@show

@section('javascript-include')
<script src="//js.pusher.com/2.2/pusher.min.js" type="text/javascript"></script>
<script>
	var pusher = new Pusher('56e8164e0555e60345c9');
	var channel = pusher.subscribe('map_{{ $game_id }}');
    channel.bind('move', function(data) {
		$('.player_' + data.pid).remove();
		$('.pos_' + data.coord).html('<i class="fa ' + data.icon + ' player_' + data.pid + '" title="' + data.pid + '"></i>');
	});
	
	channel.bind('loot', function(data) {
		 console.log('User: ' + data.pid + " Loot: " + data.loot);
	});
	
	channel.bind('dead', function(data) {
		console.log('User: ' + data.pid + " Died (You: " + data.your_l + " v Them: " + data.there_l);
		$('.player_' + data.pid).remove();
	});
	
	channel.bind('beat', function(data) {
		 console.log('User: ' + data.pid + " Beat a monster");
	});
</script>
@show