@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                {{-- Solo texto, sin iconos --}}
                <span class="fs-5 text-dark">Gestión de Usuarios - Halcón ERP</span>
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary px-3 shadow-sm" style="background-color: #5856d6; border: none;">Nuevo Usuario</a>
            </div>
            <div class="card-body">
                {{-- Alertas de sistema --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center"> {{-- Títulos centrados --}}
                                <th style="width: 80px;">ID</th>
                                <th class="text-start">Nombre</th> {{-- Texto a la izquierda para nombres --}}
                                <th class="text-start">Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="text-center"> {{-- Datos centrados para que coincidan con los títulos --}}
                                    <td>{{ $user->id }}</td>
                                    
                                    {{-- Nombre y Email alineados a la izquierda igual que su título arriba --}}
                                    <td class="text-start">{{ $user->name }}</td>
                                    <td class="text-start">{{ $user->email }}</td>
                                    
                                    <td>
                                        {{-- Rol solo como texto limpio, sin recuadro de color --}}
                                        <span class="text-dark fw-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                            {{ strtoupper($user->role->name) }}
                                        </span>
                                    </td>
                                    
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- Botón Editar --}}
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm px-3 shadow-sm border-0" style="background-color: #3b82f6;">
                                                Editar
                                            </a>
                                            
                                            {{-- Botón Borrar --}}
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-3 shadow-sm border-0" style="background-color: #ef4444;">
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados en el sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection