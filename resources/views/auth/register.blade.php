@extends('layouts.auth')

@section('content')
<div class="card p-4">
    <div class="card-body">
        <h1>{{ __('Register') }}</h1>
        <p class="text-body-secondary">Crea tu cuenta</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <svg class="icon">
                        <use xlink:href="{{ asset('assets/icons/sprites/free.svg#cil-user') }}"></use>
                    </svg>
                </span>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{ __('Name') }}">

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <svg class="icon">
                        <use xlink:href="{{ asset('assets/icons/sprites/free.svg#cil-envelope-open') }}"></use>
                    </svg>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Email Address') }}">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <svg class="icon">
                        <use xlink:href="{{ asset('assets/icons/sprites/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                </span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group mb-4">
                <span class="input-group-text">
                    <svg class="icon">
                        <use xlink:href="{{ asset('assets/icons/sprites/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                </span>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}">
            </div>

            <button class="btn btn-block btn-success w-100" type="submit">{{ __('Register') }}</button>

            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia Sesión</a></p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
