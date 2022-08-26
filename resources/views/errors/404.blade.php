@extends('layouts.auth')

@section('content')

	
	<div class="text-center">
		<img class="auth-logo" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
	</div>
	<div>			
		<div class="container mt-5 pt-5">
			<div class="alert alert-danger text-center">
				<h2 class="display-3">404</h2>
				<p class="display-5">Page not found!</p>
			</div>
		</div>
		<div class="col-md-12">
			<a href="{{ url('/') }}">
				<button type="button" class="btn-hover color-9 m-b-20"><span class="ti-angle-left"></span> {{ __('Go to Home') }}</button>
			</a>
		</div>
	</div>
	
@endsection
