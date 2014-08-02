@section('main-area')

<div class="row">
	<div class="col-md-12">

		{{ $html }}
		
	</div>
</div>

@show 

@section('javascript-include')
<script src="//js.pusher.com/2.2/pusher.min.js" type="text/javascript"></script>
<script>
	var pusher = new Pusher('56e8164e0555e60345c9');
	var channel = pusher.subscribe('map_{{ $game_id }}');
    channel.bind('move', function(data) {
		$('.pos_' + data).html('H');
	});
</script>

@show