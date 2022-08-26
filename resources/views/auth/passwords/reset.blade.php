
@extends('layouts.auth')

@section('content')


    <form method="POST" action="{{ route('password.update') }}" class="md-float-material" autocomplete="off">
		 @csrf
         <input type="hidden" name="token" value="{{ $token }}">
		<div class="text-center">
			<img class="auth-logo" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo.png">
		</div>
		<div class="auth-box">
			<div class="row m-b-20">
				<div class="col-md-12">
					<h3 class="text-left txt-primary">{{ __('Reset Password') }}</h3>
				</div>
			</div>
			@foreach($errors->all() as $error)
			   <div class="alert alert-danger">{{ $error  }}</div>
			@endforeach
			<hr/>

			<div class="input-group" id="loginField">

                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Your email" maxlength="200">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
			</div>

            <div class="input-group">
               <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password" maxlength="20">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
			</div>

			<div class="input-group">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" maxlength="20" placeholder="New Password">
			</div>

			<div class="row m-t-30">
				{{-- <div class="col-md-6">
					<a href="{{ route('register') }}" class="btn btn-secondary btn-md btn-block text-center m-b-20">
						{{ __('Register') }}
					</a>
				</div> --}}
				<div class="col-md-12">
					{{-- <button type="submit" class="btn btn-primary btn-md btn-block text-center m-b-20">
						{{ __('Reset Password') }}
					</button> --}}

                    <button type="submit" class="btn-hover color-9 m-b-20">{{ __('Reset Password') }}</button>
				</div>
			</div>
		</div>
	</form>
@endsection
