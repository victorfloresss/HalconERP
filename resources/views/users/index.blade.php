@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <span class="fs-5" style="color: #402718;">Gestión de Usuarios - Halcón ERP</span>
                <a href="{{ route('users.create') }}" class="btn btn-sm px-3 shadow-sm text-white" style="background-color: #8C3E53; border: none; border-radius: 12px; font-weight: bold;">Nuevo Usuario</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 5px solid #8C3E53 !important; background: white; color: #402718;">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 5px solid #D99152 !important; background: white; color: #402718;">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead style="background-color: #402718; color: #BFB195;">
                            <tr class="text-center">
                                <th style="width: 80px;">ID</th>
                                <th class="text-start">Nombre</th>
                                <th class="text-start">Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="text-center">
                                    <td class="fw-bold" style="color: #402718;">{{ $user->id }}</td>
                                    
                                    <td class="text-start" style="color: #402718;">{{ $user->name }}</td>
                                    <td class="text-start text-muted">{{ $user->email }}</td>
                                    
                                    <td>
                                        <span class="fw-bold" style="font-size: 0.85rem; letter-spacing: 0.5px; color: #8C5B3F;">
                                            {{ strtoupper($user->role->name) }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                                    
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm px-3 shadow-sm border-0 text-white" style="background-color: #D99152;">
                                                Editar
                                            </a>
                                            
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm px-3 shadow-sm border-0 text-white" style="background-color: #8C3E53;">
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