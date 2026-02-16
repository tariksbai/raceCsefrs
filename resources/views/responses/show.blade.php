@extends('layouts.app')

@section('title', 'Réponse #' . $response->id)
@section('page-title', 'Réponse #' . $response->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.responses.index', $response->form->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour aux r&eacute;ponses
    </a>
</div>

{{-- Response Details Card --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-info-circle me-2"></i> D&eacute;tails de la r&eacute;ponse
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width: 160px;">Formulaire</th>
                            <td>
                                <a href="{{ route('admin.internal-forms.show', $response->form->id) }}">
                                    {{ $response->form->title }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Utilisateur</th>
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
                        </tr>
                        <tr>
                            <th class="text-muted">Date</th>
                            <td>
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $response->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Adresse IP</th>
                            <td>
                                <code>{{ $response->ip_address ?? 'N/A' }}</code>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Field Values Table --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check me-2"></i> Valeurs des champs
        </h5>
    </div>
    <div class="card-body p-0">
        @if($response->values && $response->values->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Champ</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($response->values as $value)
                            <tr>
                                <td>
                                    <strong>{{ $value->field->label ?? 'Champ supprim&eacute;' }}</strong>
                                    @if($value->field)
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-tag me-1"></i>{{ $value->field->type }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if(is_array(json_decode($value->value, true)))
                                        <ul class="list-unstyled mb-0">
                                            @foreach(json_decode($value->value, true) as $item)
                                                <li><i class="bi bi-check2 me-1 text-success"></i>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @elseif(!empty($value->value))
                                        {{ $value->value }}
                                    @else
                                        <span class="text-muted fst-italic">Aucune valeur</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Aucune valeur enregistr&eacute;e pour cette r&eacute;ponse.</p>
            </div>
        @endif
    </div>
</div>

{{-- Back Button --}}
<div class="mt-4">
    <a href="{{ route('admin.responses.index', $response->form->id) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection
