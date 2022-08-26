@extends('layouts.auth')

@section('content')

	<div class="md-float-material">
		<div class="text-center">
			<img class="auth-logo" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
		</div>
		<div class="auth-box">
			<div class="row m-b-20">
				<div class="col-md-12">
					<h3 class="text-left txt-primary text-center">{{ __('Good Job !') }}</h3>
				</div>
			</div>
			@if(session()->has('status'))
				
				<div class="alert alert-success text-center">{{ session()->get('status') }}</div>
				
			@else
				
				@foreach($errors->all() as $error)
				   <div class="alert alert-danger">{{ $error  }}</div>
				@endforeach
				
				@if(session()->get('error'))			
					<div class="alert alert-danger alert-dismissible">
					  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					  <h5><i class="icon fas fa-ban"></i> Error!</h5>
					 {{ session()->get('error') }}
					</div>			
				@endif
				
			@endif
			
			<hr/>

		</div>
	</div>
    
@endsection