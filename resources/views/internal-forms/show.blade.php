@extends('layouts.app')

@section('title', 'Détails du formulaire interne')

@section('page-title', 'Détails du formulaire interne')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails du formulaire interne</h2>
    <a href="{{ route('admin.internal-forms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $form->title }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Titre</th>
                        <td>{{ $form->title }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td>{{ $form->description ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Groupe</th>
                        <td>{{ $form->group->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Statut</th>
                        <td>
                            @if($form->status === 'draft')
                                <span class="badge bg-warning text-dark">Brouillon</span>
                            @elseif($form->status === 'active')
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Public</th>
                        <td>
                            @if($form->is_public)
                                <span class="text-success">Oui</span>
                            @else
                                <span class="text-muted">Non</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Réponses anonymes</th>
                        <td>
                            @if($form->allow_anonymous)
                                <span class="text-success">Oui</span>
                            @else
                                <span class="text-muted">Non</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Date de début</th>
                        <td>{{ $form->start_date ? \Carbon\Carbon::parse($form->start_date)->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Date de fin</th>
                        <td>{{ $form->end_date ? \Carbon\Carbon::parse($form->end_date)->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Créé le</th>
                        <td>{{ $form->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Modifié le</th>
                        <td>{{ $form->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.internal-forms.edit', $form) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.internal-forms.builder', $form) }}" class="btn btn-primary">
            <i class="bi bi-tools me-1"></i> Constructeur
        </a>
        <a href="{{ route('admin.responses.index', $form) }}" class="btn btn-info">
            <i class="bi bi-chat-square-text me-1"></i> Réponses
        </a>
        <a href="{{ route('admin.internal-forms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>

{{-- Liste des champs --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Champs du formulaire</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ordre</th>
                    <th>Libellé</th>
                    <th>Type</th>
                    <th>Obligatoire</th>
                </tr>
            </thead>
            <tbody>
                @forelse($form->fields as $field)
                <tr>
                    <td>{{ $field->order ?? $loop->iteration }}</td>
                    <td>{{ $field->label }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $field->type }}</span>
                    </td>
                    <td>
                        @if($field->is_required ?? $field->required ?? false)
                            <span class="text-success">Oui</span>
                        @else
                            <span class="text-muted">Non</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Aucun champ défini. Utilisez le constructeur pour ajouter des champs.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
