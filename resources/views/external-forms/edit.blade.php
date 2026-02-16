@extends('layouts.app')

@section('title', 'Modifier le formulaire externe')

@section('page-title', 'Modifier le formulaire externe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifier le formulaire : {{ $form->title }}</h2>
    <a href="{{ route('admin.external-forms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.external-forms.update', $form) }}">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Titre --}}
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $form->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Groupe --}}
                <div class="col-md-6 mb-3">
                    <label for="group_id" class="form-label">Groupe</label>
                    <select name="group_id" id="group_id" class="form-select @error('group_id') is-invalid @enderror">
                        <option value="">-- Sélectionner un groupe --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id', $form->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $form->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Code iframe --}}
                <div class="col-12 mb-3">
                    <label for="iframe_code" class="form-label">Code iframe</label>
                    <textarea name="iframe_code" id="iframe_code" rows="4" class="form-control @error('iframe_code') is-invalid @enderror" style="font-family: monospace;">{{ old('iframe_code', $form->iframe_code) }}</textarea>
                    @error('iframe_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Statut --}}
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $form->status) === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $form->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Public --}}
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $form->is_public) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Formulaire public</label>
                    </div>
                </div>

                {{-- Date début --}}
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Date de début</label>
                    <input type="datetime-local" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $form->start_date ? \Carbon\Carbon::parse($form->start_date)->format('Y-m-d\TH:i') : '') }}">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date fin --}}
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $form->end_date ? \Carbon\Carbon::parse($form->end_date)->format('Y-m-d\TH:i') : '') }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.external-forms.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
