@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            {{-- Header con color Café y texto Arena --}}
            <div class="card-header fw-bold py-3" style="background-color: #402718; color: #BFB195;">
                <span><i class="icon me-2 cil-user-plus"></i> Crear Nuevo Empleado</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold" style="color: #402718;">Nombre Completo</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required style="border-color: #BFB195;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold" style="color: #402718;">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required style="border-color: #BFB195;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-bold" style="color: #402718;">Rol del Empleado</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required style="border-color: #BFB195;">
                            <option value="" selected disabled>Selecciona un rol...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold" style="color: #402718;">Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required style="border-color: #BFB195;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold" style="color: #402718;">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required style="border-color: #BFB195;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2 px-4" style="border-radius: 12px;">Cancelar</a>
                        {{-- Botón en color Vino redondeado --}}
                        <button type="submit" class="btn text-white px-4 shadow-sm" style="background-color: #8C3E53; border: none; border-radius: 12px; font-weight: bold;">
                            Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection