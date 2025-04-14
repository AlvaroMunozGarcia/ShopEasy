@extends('layouts.admin') {{-- Usar tu layout personalizado --}}

@section('content') {{-- Contenido principal para el @yield('content') --}}
    <h1>Client Details: {{ $client->name }}</h1>

    <div class="card mt-3">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $client->id }}</dd>

                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $client->name }}</dd>

                <dt class="col-sm-3">DNI</dt>
                <dd class="col-sm-9">{{ $client->dni }}</dd>

                <dt class="col-sm-3">RUC</dt>
                <dd class="col-sm-9">{{ $client->ruc ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $client->address ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $client->phone ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $client->email ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $client->created_at ? $client->created_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $client->updated_at ? $client->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">Edit</a>
             <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display: inline-block;" class="float-end"> {{-- float-end para Bootstrap 5 --}}
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this client?')">Delete</button>
            </form>
        </div>
    </div>
@endsection
