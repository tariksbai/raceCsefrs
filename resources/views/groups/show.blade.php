@extends('layouts.app')

@section('title', 'Détails du groupe')

@section('page-title', 'Détails du groupe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails du groupe</h2>
    <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $group->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Nom</th>
                        <td>{{ $group->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td>{{ $group->description ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Créé le</th>
                        <td>{{ $group->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Modifié le</th>
                        <td>{{ $group->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                {{-- Permissions --}}
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Permissions</h6>
                    @forelse($group->permissions as $permission)
                        <span class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                    @empty
                        <p class="text-muted fst-italic">Aucune permission attribuée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Membres --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Membres du groupe</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($group->users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted py-4">Aucun membre dans ce groupe.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection
