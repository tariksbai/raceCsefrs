@extends('layouts.app')

@section('page-title', 'Modifier l\'utilisateur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifier l'utilisateur : {{ $user->name }}</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Nom --}}
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Laisser vide pour conserver l'actuel">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Laisser vide pour ne pas modifier le mot de passe.</div>
                </div>

                {{-- Confirmation du mot de passe --}}
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmer le nouveau mot de passe">
                </div>

                {{-- Statut --}}
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Type d'utilisateur --}}
                <div class="col-md-6 mb-3">
                    <label for="user_type" class="form-label">Type d'utilisateur <span class="text-danger">*</span></label>
                    <select name="user_type" id="user_type" class="form-select @error('user_type') is-invalid @enderror" required>
                        <option value="admin" {{ old('user_type', $user->user_type) === 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="user" {{ old('user_type', $user->user_type) === 'user' ? 'selected' : '' }}>Utilisateur</option>
                    </select>
                    @error('user_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- R&ocirc;les --}}
            <div class="mb-3">
                <label class="form-label">R&ocirc;les</label>
                @error('roles')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="row">
                    @foreach($roles as $role)
                    <div class="col-md-4 col-lg-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Groupes --}}
            <div class="mb-3">
                <label class="form-label">Groupes</label>
                @error('groups')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="row">
                    @foreach($groups as $group)
                    <div class="col-md-4 col-lg-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="groups[]" value="{{ $group->id }}" id="group_{{ $group->id }}"
                                {{ in_array($group->id, old('groups', $user->groups->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="group_{{ $group->id }}">{{ $group->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Mettre &agrave; jour
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
