@extends('layouts.admin') {{-- Usar tu layout personalizado --}}

@section('content') {{-- Contenido principal para el @yield('content') --}}
    {{-- El título se moverá dentro del card-header para un diseño más integrado --}}

    <div class="card mt-3"> {{-- Añadido margen superior para separar del título --}}
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Client List</h4> {{-- Título de la tarjeta --}}
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add New Client {{-- Icono añadido y texto --}}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive"> {{-- Para mejor visualización en pantallas pequeñas --}}
                <table class="table table-bordered table-striped table-hover"> {{-- Añadido table-hover --}}
                    <thead class="table-dark"> {{-- Clase añadida para invertir colores del encabezado --}}
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DNI</th>
                            <th>RUC</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->dni }}</td>
                                <td>{{ $client->ruc ?? 'N/A' }}</td>
                                <td>{{ $client->email ?? 'N/A' }}</td>
                                <td>{{ $client->phone ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this client?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No clients found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- Optional: Add pagination links if using pagination --}}
            {{-- $clients->links() --}}
        </div>
    </div>

@endsection
