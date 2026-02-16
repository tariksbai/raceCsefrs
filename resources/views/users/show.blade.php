@extends('layouts.app')

@section('page-title', 'D&eacute;tails de l\'utilisateur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>D&eacute;tails de l'utilisateur</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $user->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%;">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nom</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Type</th>
                        <td>
                            @if($user->user_type === 'admin')
                                <span class="badge bg-dark">Administrateur</span>
                            @else
                                <span class="badge bg-secondary">Utilisateur</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Statut</th>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Cr&eacute;&eacute; le</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Modifi&eacute; le</th>
                        <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                {{-- R&ocirc;les --}}
                <div class="mb-4">
                    <h6 class="text-muted mb-2">R&ocirc;les</h6>
                    @forelse($user->roles as $role)
                        <span class="badge bg-primary me-1 mb-1">{{ $role->name }}</span>
                    @empty
                        <p class="text-muted fst-italic">Aucun r&ocirc;le attribu&eacute;.</p>
                    @endforelse
                </div>

                {{-- Groupes --}}
                <div>
                    <h6 class="text-muted mb-2">Groupes</h6>
                    @forelse($user->groups as $group)
                        <span class="badge bg-info text-dark me-1 mb-1">{{ $group->name }}</span>
                    @empty
                        <p class="text-muted fst-italic">Aucun groupe attribu&eacute;.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour &agrave; la liste
        </a>
    </div>
</div>
@endsection
