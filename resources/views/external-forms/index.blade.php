@extends('layouts.app')

@section('title', 'Gestion des Formulaires Externes')

@section('page-title', 'Gestion des Formulaires Externes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Formulaires Externes</h2>
    <a href="{{ route('admin.external-forms.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ajouter
    </a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Titre</th>
                    <th>Groupe</th>
                    <th>Statut</th>
                    <th>Public</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                <tr>
                    <td>{{ $form->title }}</td>
                    <td>{{ $form->group->name ?? '—' }}</td>
                    <td>
                        @if($form->status === 'active')
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </td>
                    <td>
                        @if($form->is_public)
                            <span class="text-success">Oui</span>
                        @else
                            <span class="text-muted">Non</span>
                        @endif
                    </td>
                    <td>{{ $form->start_date ? \Carbon\Carbon::parse($form->start_date)->format('d/m/Y H:i') : '—' }}</td>
                    <td>{{ $form->end_date ? \Carbon\Carbon::parse($form->end_date)->format('d/m/Y H:i') : '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.external-forms.show', $form) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.external-forms.edit', $form) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.external-forms.destroy', $form) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formulaire ?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Aucun formulaire externe trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($forms->hasPages())
    <div class="card-footer">
        {{ $forms->links() }}
    </div>
    @endif
</div>
@endsection
