@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('page-title', 'Gestion des Groupes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Groupes</h2>
    <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ajouter un groupe
    </a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Membres</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td>{{ $group->name }}</td>
                    <td>{{ Str::limit($group->description, 50) ?? '—' }}</td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $group->users_count ?? $group->users->count() }}</span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $group->permissions_count ?? $group->permissions->count() }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Aucun groupe trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($groups->hasPages())
    <div class="card-footer">
        {{ $groups->links() }}
    </div>
    @endif
</div>
@endsection
