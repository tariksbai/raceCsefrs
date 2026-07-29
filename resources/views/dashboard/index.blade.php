@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Tableau de bord</h1>
        <span class="text-muted">{{ now()->translatedFormat('l d F Y') }}</span>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Users --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #0d6efd;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-1">Utilisateurs</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-people fs-4 text-white"></i>
                        </div>
                    </div>
                    <small class="text-white-50">Total des utilisateurs inscrits</small>
                </div>
            </div>
        </div>

        {{-- External Forms --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #198754;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-1">Formulaires externes</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalExternalForms) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-link-45deg fs-4 text-white"></i>
                        </div>
                    </div>
                    <small class="text-white-50">Liens vers des formulaires externes</small>
                </div>
            </div>
        </div>

        {{-- Internal Forms --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #fd7e14;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-1">Formulaires internes</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalInternalForms) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-ui-checks fs-4 text-white"></i>
                        </div>
                    </div>
                    <small class="text-white-50">Formulaires créés sur la plateforme</small>
                </div>
            </div>
        </div>

        {{-- Total Responses --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #6f42c1;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-1">Réponses</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalResponses) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-chat-square-text fs-4 text-white"></i>
                        </div>
                    </div>
                    <small class="text-white-50">Total des réponses reçues</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Recent Activity --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Activité récente
                    </h5>
                    <span class="badge bg-secondary">{{ $recentResponses->count() }} dernières réponses</span>
                </div>
                <div class="card-body p-0">
                    @if ($recentResponses->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Aucune réponse récente</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Formulaire</th>
                                        <th scope="col">Utilisateur</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentResponses as $response)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $response->id }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-file-earmark-text me-1 text-muted"></i>
                                                {{ $response->form->title ?? 'Formulaire supprimé' }}
                                            </td>
                                            <td>
                                                <i class="bi bi-person-circle me-1 text-muted"></i>
                                                {{ $response->user->name ?? 'Anonyme' }}
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ $response->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-lightning me-2"></i>Accès rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.external-forms.create') }}" class="btn btn-outline-success text-start">
                            <i class="bi bi-link-45deg me-2"></i>Nouveau formulaire externe
                        </a>
                        <a href="{{ route('admin.internal-forms.create') }}" class="btn btn-outline-warning text-start">
                            <i class="bi bi-ui-checks me-2"></i>Nouveau formulaire interne
                        </a>
                        <a href="{{ route('admin.external-forms.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-list-ul me-2"></i>Gérer les formulaires externes
                        </a>
                        <a href="{{ route('admin.internal-forms.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-list-check me-2"></i>Gérer les formulaires internes
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-people me-2"></i>Gérer les utilisateurs
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-shield-lock me-2"></i>Gérer les rôles
                        </a>
                    </div>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-2"></i>Résumé
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total formulaires
                            <span class="badge bg-primary rounded-pill">{{ $totalExternalForms + $totalInternalForms }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Formulaires externes
                            <span class="badge bg-success rounded-pill">{{ $totalExternalForms }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Formulaires internes
                            <span class="badge bg-warning rounded-pill">{{ $totalInternalForms }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Réponses
                            <span class="badge bg-purple rounded-pill" style="background-color: #6f42c1 !important;">{{ $totalResponses }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
