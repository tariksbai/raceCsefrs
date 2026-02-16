@extends('layouts.public')

@section('title', 'Accueil')

@section('content')
{{-- Hero Section --}}
<section class="text-white py-5" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); margin-top: -2rem;">
    <div class="container">
        <div class="row align-items-center min-vh-50 py-5">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-3">
                    Plateforme Citoyenne Formulaires
                </h1>
                <p class="lead mb-4 opacity-75">
                    La plateforme centralisee pour creer, gerer et diffuser vos formulaires citoyens.
                    Simplifiez la collecte de donnees et ameliorez la communication avec les citoyens.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i>S'inscrire
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <i class="bi bi-clipboard2-data" style="font-size: 12rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section class="py-5 bg-light" id="fonctionnalites">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Fonctionnalites de la plateforme</h2>
            <p class="text-muted lead">Tout ce dont vous avez besoin pour gerer vos formulaires</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-link-45deg fs-1 text-success"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Formulaires Externes</h5>
                        <p class="card-text text-muted">
                            Integrez des formulaires heberges sur d'autres plateformes via iframe.
                            Centralisez tous vos liens en un seul endroit.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-ui-checks fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Formulaires Internes</h5>
                        <p class="card-text text-muted">
                            Creez des formulaires personnalises avec un constructeur drag & drop intuitif.
                            Ajoutez des champs texte, choix multiples, dates et bien plus.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-lock fs-1 text-warning"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Gestion Avancee</h5>
                        <p class="card-text text-muted">
                            Gerez les roles, groupes et permissions de votre equipe.
                            Controlez qui peut creer, modifier ou consulter les formulaires.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Call to Action --}}
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Pret a commencer ?</h2>
        <p class="lead mb-4 opacity-75">
            Rejoignez la plateforme et commencez a creer vos formulaires des aujourd'hui.
        </p>
        @guest
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-person-plus me-2"></i>Creer un compte
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                </a>
            </div>
        @else
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-speedometer2 me-2"></i>Acceder au tableau de bord
            </a>
        @endguest
    </div>
</section>
@endsection
