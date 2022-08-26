@extends('layouts.auth')

@section('content')

	<form method="POST" action="{{ route('login') }}" class="md-float-material" autocomplete="off">
		 @csrf
		<div class="text-center">
			<img src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
		</div>
		<div class="auth-box">
			<div class="row m-b-20">
				<div class="col-md-6">
					<h3 class="text-left txt-primary">{{ __('Login') }}</h3>
				</div>
				<div class="col-md-6 text-right pt-4">
					Login with
					<select class="form-control-loginWith" name="loginWith" id="loginWith">
						<option value="1" {{ old('loginWith') == 1 ? "selected" : "" }}>Email</option>
						<option value="2" {{ old('loginWith') == 2 ? "selected" : "" }}>Phone</option>
					</select>
				</div>
			</div>
			@foreach($errors->all() as $error)
			   <div class="alert alert-danger">{{ $error  }}</div>
			@endforeach
			<hr/>			
			
			<div class="input-group" id="loginField">
			@if(!empty(old('loginWith')))
				@if(old('loginWith') == 1)
					<input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email address" maxlength="200">
				@endif
				@if(old('loginWith') == 2)
					<input id="email" type="text" class="form-control USphone onlynumber" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your phone number" maxlength="14">
				@endif
			@else				
                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email address" maxlength="200">
			@endif
			</div>
			<div class="input-group">
				<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Your password" maxlength="20">
			</div>
			<div class="row m-t-25 text-left">
				<div class="col-sm-7 col-xs-12">
					<div class="checkbox-fade fade-in-primary">
						<label>
							<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
							<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
							<span class="text-inverse">{{ __('Remember Me') }}</span>
						</label>
					</div>
				</div>
				@if (Route::has('password.request'))
					<div class="col-sm-5 col-xs-12 forgot-phone text-right">
						<a class="text-right f-w-600 text-inverse" href="{{ route('password.request') }}">
							{{ __('Forgot Your Password?') }}
						</a>
					</div>
				@endif
			</div>
			<div class="row m-t-30">
				{{-- <div class="col-md-6">
					<a href="{{ route('register') }}" class="btn btn-secondary btn-md btn-block text-center m-b-20">
						{{ __('Register') }}
					</a>
				</div> --}}
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary btn-md btn-block text-center m-b-20">
						{{ __('Login') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection
