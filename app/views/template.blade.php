<!DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css">
	<title>MUD Panel</title>
	@section('head-area')
    @show
    
</head>
<body>

	<div class="container">

		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li class="<?php if (Request::is('/')) { echo "active"; } ?>"><a href="{{ url("/") }}">Home</a></li>
				<li class="<?php if (Request::is('maps')) { echo "active"; } ?>"><a href="{{ url("maps") }}">Maps</a></li>
			</ul>
			<h3 class="text-muted">MUD</h3>
		</div>

		<div class="content">		
			@section('main-area')
        	@show        	
        </div>
        
    </div>
    
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    @section('javascript-include')
    @show
    
</body>
</html>