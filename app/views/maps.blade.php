@section('main-area')

<div class="row">
	<div class="col-md-12">

	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Map Size</th>
				<th>Created Date</th>
				<th>Options</th>
				<th>json</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($maps as $map)
			<tr>
				<td>{{ $map['id'] }}</td>
				<td>{{ $map['size'] }}</td>
				<td>{{ $map['created_at'] }}</td>
				<td><a href='{{ action('PagesController@prettyView', array($map['id'])) }}'>View</a></td>
				<td><a href='{{ action('PagesController@jsonView', array($map['id'])) }}'>View</a></td>
			</tr>
			@endforeach
		</tbody>
		</table>
		
	</div>
</div>

@show 