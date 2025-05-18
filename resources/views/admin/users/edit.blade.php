@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Editar Usuario')

@section('page_header')
    Editar Usuario: <span class="text-muted">{{ $user->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection

@section('content')
{{-- El H1 anterior se elimina ya que @page_header lo maneja --}}
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT') {{-- Importante para la actualización --}}

            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password (Optional)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <small class="form-text text-muted">Leave blank to keep the current password.</small>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>

            <div class="mb-3">
                <label for="roles" class="form-label">Roles <span class="text-danger">*</span></label>
                <select multiple class="form-control @error('roles') is-invalid @enderror" id="roles" name="roles[]" required>
                    @foreach ($roles as $id => $name)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', $userRoles)) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('roles')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <small class="form-text text-muted">Hold down Ctrl (or Cmd on Mac) to select multiple roles.</small>
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
