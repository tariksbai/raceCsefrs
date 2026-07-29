@extends('layouts.app')

@section('title', 'Détails du formulaire externe')

@section('page-title', 'Détails du formulaire externe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails du formulaire externe</h2>
    <a href="{{ route('admin.external-forms.index') }}" class="btn btn-outline-secondary">
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
                            @if($form->status === 'active')
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
        <a href="{{ route('admin.external-forms.edit', $form) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.external-forms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>

{{-- Aperçu iframe --}}
@if($form->iframe_code)
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Aperçu du formulaire</h5>
    </div>
    <div class="card-body">
        <div class="ratio ratio-16x9">
            {!! $form->iframe_code !!}
        </div>
    </div>
</div>
@endif
@endsection
