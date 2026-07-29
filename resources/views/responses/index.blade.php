@extends('layouts.app')

@section('title', 'Réponses - ' . $form->title)
@section('page-title', 'Réponses - ' . $form->title)

@section('content')
<div class="mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
    <a href="{{ route('admin.internal-forms.show', $form->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour au formulaire
    </a>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.responses.export.csv', $form->id) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-filetype-csv me-1"></i> Exporter CSV
        </a>
        <a href="{{ route('admin.responses.export.excel', $form->id) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exporter Excel
        </a>
        <a href="{{ route('admin.responses.statistics', $form->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart-line me-1"></i> Statistiques
        </a>
    </div>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.responses.index', $form->id) }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Date de d&eacute;but</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="user" class="form-label">Utilisateur</label>
                    <input type="text" class="form-control" id="user" name="user"
                           value="{{ request('user') }}" placeholder="Nom de l'utilisateur">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.responses.index', $form->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> R&eacute;initialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Responses Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="bi bi-chat-square-text me-2"></i> Liste des r&eacute;ponses
        </h5>
        <span class="badge bg-primary">{{ $responses->total() }} r&eacute;ponse(s)</span>
    </div>
    <div class="card-body p-0">
        @if($responses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Utilisateur</th>
                            <th>Date</th>
                            <th style="width: 120px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $response->id }}</span>
                                </td>
                                <td>
                                    @if($response->user)
                                        <i class="bi bi-person me-1"></i>
                                        {{ $response->user->name }}
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-incognito me-1"></i> Anonyme
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $response->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.responses.show', [$form->id, $response->id]) }}"
                                       class="btn btn-outline-info btn-sm" title="Voir la r&eacute;ponse">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Aucune r&eacute;ponse trouv&eacute;e pour ce formulaire.</p>
            </div>
        @endif
    </div>

    @if($responses->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $responses->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
