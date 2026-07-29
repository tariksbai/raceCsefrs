@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('page-title', 'Gestion des Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Permissions</h2>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ajouter une permission
    </a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td><code>{{ $permission->slug }}</code></td>
                    <td>{{ $permission->description ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Aucune permission trouvée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($permissions->hasPages())
    <div class="card-footer">
        {{ $permissions->links() }}
    </div>
    @endif
</div>
@endsection
