@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3" style="overflow: hidden;">
                {{-- Header con fondo Café y texto Arena --}}
                <div class="card-header py-3 border-bottom" style="background-color: #402718;">
                    <h5 class="card-title mb-0 fw-bold" style="color: #BFB195;">Editar Empleado</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #402718;">Nombre Completo</label>
                            <input type="text" name="name" class="form-control form-control-lg fs-6 @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required placeholder="Ej. Juan Pérez" style="border-color: #BFB195;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #402718;">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control form-control-lg fs-6 @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required placeholder="email@halcon.com" style="border-color: #BFB195;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #402718;">Rol del Empleado</label>
                            <select name="role_id" class="form-select form-select-lg fs-6 @error('role_id') is-invalid @enderror" required style="border-color: #BFB195;">
                                <option value="" disabled>Selecciona un rol...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold" style="color: #402718;">Nueva Contraseña (Opcional)</label>
                                <input type="password" name="password" class="form-control form-control-lg fs-6 @error('password') is-invalid @enderror" 
                                       placeholder="••••••" style="border-color: #BFB195;">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold" style="color: #402718;">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg fs-6" 
                                       placeholder="••••••" style="border-color: #BFB195;">
                            </div>
                        </div>

                        <div class="border-top pt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary px-4 py-2" style="border-radius: 12px; border: none;">Cancelar</a>
                            <button type="submit" class="btn text-white px-4 py-2 shadow-sm" style="background-color: #8C3E53; border: none; border-radius: 12px; font-weight: bold;">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection