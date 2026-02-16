@extends('layouts.app')

@section('title', 'Détails de la permission')

@section('page-title', 'Détails de la permission')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails de la permission</h2>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $permission->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Nom</th>
                        <td>{{ $permission->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Slug</th>
                        <td><code>{{ $permission->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td>{{ $permission->description ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Créé le</th>
                        <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Modifié le</th>
                        <td>{{ $permission->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="text-muted mb-2">Rôles associés</h6>
                @forelse($permission->roles as $role)
                    <span class="badge bg-primary me-1 mb-1">{{ $role->name }}</span>
                @empty
                    <p class="text-muted fst-italic">Aucun rôle associé.</p>
                @endforelse

                <h6 class="text-muted mb-2 mt-3">Groupes associés</h6>
                @forelse($permission->groups as $group)
                    <span class="badge bg-secondary me-1 mb-1">{{ $group->name }}</span>
                @empty
                    <p class="text-muted fst-italic">Aucun groupe associé.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash me-1"></i> Supprimer
            </button>
        </form>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection
