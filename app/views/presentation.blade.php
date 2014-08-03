<!DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ asset('style.css') }}">
	<title>MUD Panel</title>
	@section('head-area')
    @show
    
</head>
<body>

	<div class="container-fluid">

		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="">@jakelprice</a></li>
				<li><a href="">@curtis_h</a></li>			
				<li><a href="">@ukmadlz</a></li>
				<li><a href="">@hazanjon</a></li>
			</ul>
			<h3 class="text-muted">Super Dooper Twitter MUD</h3>
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