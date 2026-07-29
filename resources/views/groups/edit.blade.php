@extends('layouts.app')

@section('title', 'Modifier le groupe')

@section('page-title', 'Modifier le groupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifier le groupe : {{ $group->name }}</h2>
    <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.groups.update', $group) }}">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Nom --}}
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $group->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-md-6 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $group->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Permissions --}}
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                @error('permissions')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="row">
                    @foreach($permissions as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                {{ in_array($permission->id, old('permissions', $group->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Utilisateurs --}}
            <div class="mb-3">
                <label class="form-label">Utilisateurs</label>
                @error('users')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="row">
                    @foreach($users as $user)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}"
                                {{ in_array($user->id, old('users', $group->users->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="user_{{ $user->id }}">{{ $user->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
