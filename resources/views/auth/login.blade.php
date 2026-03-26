@extends('layouts.auth')

@section('content')
<div class="card p-4">
    <div class="card-body">
        <h1>{{ __('Login') }}</h1>
        <p class="text-body-secondary">Inicia sesión en tu cuenta</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <svg class="icon">
                        <use xlink:href="{{ asset('assets/icons/sprites/free.svg#cil-user') }}"></use>
                    </svg>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('Email Address') }}">

                @error('email')
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
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <button type="submit" class="btn btn-primary px-4">
                        {{ __('Login') }}
                    </button>
                </div>
                <div class="col-6 text-end">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </div>
            @if (Route::has('register'))
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
