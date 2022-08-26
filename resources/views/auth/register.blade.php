@extends('layouts.auth')

@section('content')

<form class="md-float-material" action="{{ route('register') }}" method="post" autocomplete="off">
	@csrf
	<div class="text-center">
		<img src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
	</div>
	<div class="auth-box">
		<div class="row m-b-20">
			<div class="col-md-12">
				<h3 class="text-center txt-primary">Sign up. It is fast and easy.</h3>
			</div>
		</div>
		<hr/>
		<div class="input-group">
			<input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="Enter your first name" maxlength="70">
			<span class="md-line"></span>
			@error('first_name')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="input-group">
			<input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required placeholder="Enter your last name" maxlength="70">
			<span class="md-line"></span>
			@error('last_name')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="input-group">
			<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email address" maxlength="200">
			<span class="md-line"></span>
			@error('email')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="input-group">
			<input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" required placeholder="Enter your phone number" maxlength="15">
			<span class="md-line"></span>
			@error('phone')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="input-group">
			<select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
				<!--<option value="1">Super Admin</option>-->
				<option value="2">Admin</option>
				<option value="3">Business</option>
                <option value="4">Employee</option>
                <option value="5">Consumer</option>
			</select>
			<span class="md-line"></span>
			@error('role')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="input-group">
			<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your password" maxlength="20">

			<span class="md-line"></span>
			@error('password')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="input-group">
			<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Enter your confirm password" maxlength="20">
			<span class="md-line"></span>
		</div>

		<div class="row m-t-30">
			<div class="col-md-6">
				<a href="{{ route('login') }}" class="btn btn-secondary btn-md btn-block text-center m-b-20">
					{{ __('Login') }}
				</a>
			</div>
			<div class="col-md-6">
				<button type="submit" class="btn btn-primary btn-md btn-block text-center m-b-20">
					{{ __('Register') }}
				</button>
			</div>
		</div>

	</div>
</form>
@endsection
