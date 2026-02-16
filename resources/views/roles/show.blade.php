@extends('layouts.app')

@section('title', 'Détails du rôle')

@section('page-title', 'Détails du rôle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails du rôle</h2>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $role->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Nom</th>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Slug</th>
                        <td><code>{{ $role->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td>{{ $role->description ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Créé le</th>
                        <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Modifié le</th>
                        <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="text-muted mb-2">Permissions</h6>
                @forelse($role->permissions as $permission)
                    <span class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                @empty
                    <p class="text-muted fst-italic">Aucune permission attribuée.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection
