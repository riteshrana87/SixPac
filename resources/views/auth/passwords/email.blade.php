@extends('layouts.auth')

@section('content')

@if (session('status'))
	<div class="alert alert-success" role="alert">
		{{ session('status') }}
	</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="md-float-material" autocomplete="off">
	 @csrf
	<div class="text-center">
		<img class="auth-logo" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
	</div>
	<div class="auth-box">
		<div class="row m-b-20">
			<div class="col-md-12">
				<h3 class="text-left txt-primary">{{ __('Reset Password') }}</h3>
			</div>
		</div>
		<hr/>
		<div class="input-group">
			<input id="email" type="email" class="field-100 form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Your email address" autofocus maxlength="200">
			@error('email')
				<span class="invalid-feedback span-error" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="row m-t-30">
			<div class="col-md-4">
				{{-- <a href="{{ route('login') }}" class="btn btn-secondary btn-md btn-block text-center m-b-20">
					{{ __('Back to Login') }}
                </a> --}}
                <a href="{{ route('login') }}"><button type="button" class="btn-hover color-11 m-b-20">{{ __('Back to Login') }}</button></a>
			</div>
            <div class="col-md-8">
				{{-- <button type="submit" class="btn btn-primary btn-md btn-block text-center m-b-20">
					{{ __('Send Password Reset Link') }}
				</button> --}}
                <button type="submit" class="btn-hover color-9 m-b-20">{{ __('Send Password Reset Link') }}</button>
			</div>
		</div>
		<!--
		<hr/>
		<div class="row">
			<div class="col-md-10">
				<p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p>
				<p class="text-inverse text-left"><b>Your Authentication Team</b></p>
			</div>
			<div class="col-md-2">
				<img src="{{ asset('backend/assets/images/auth/Logo-small-bottom.png') }}" alt="small-logo.png">
			</div>
		</div>
		-->
	</div>
</form>
@endsection
