@extends('layouts.app')

@section('title', 'Constructeur de formulaire')

@section('page-title', 'Constructeur de formulaire')

@push('styles')
<style>
    /* ── Builder Layout ── */
    .builder-container {
        display: flex;
        gap: 1rem;
        min-height: calc(100vh - 200px);
    }

    /* ── Toolbox (Left Sidebar) ── */
    .builder-toolbox {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        height: fit-content;
        position: sticky;
        top: 80px;
    }

    .builder-toolbox h6 {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #6c63ff;
    }

    .field-type {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 0.75rem;
        margin-bottom: 0.35rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        cursor: grab;
        font-size: 0.875rem;
        color: #4a5568;
        transition: all 0.15s ease;
        user-select: none;
    }

    .field-type:hover {
        background: #eef0ff;
        border-color: #6c63ff;
        color: #6c63ff;
    }

    .field-type:active {
        cursor: grabbing;
    }

    .field-type i {
        font-size: 1rem;
        width: 1.25rem;
        text-align: center;
        color: #6c63ff;
    }

    /* ── Canvas (Center) ── */
    .builder-canvas {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        min-height: 400px;
        padding: 1rem;
    }

    .builder-canvas h6 {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #6c63ff;
    }

    .canvas-drop-zone {
        min-height: 300px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .canvas-drop-zone.drag-over {
        border-color: #6c63ff;
        background-color: #f0eeff;
    }

    .canvas-drop-zone.has-fields {
        display: block;
        border: none;
        padding: 0;
    }

    .canvas-placeholder {
        text-align: center;
        color: #a0aec0;
    }

    .canvas-placeholder i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .canvas-placeholder p {
        font-size: 0.95rem;
        margin: 0;
    }

    /* ── Field Card on Canvas ── */
    .field-card {
        background: #ffffff;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .field-card:hover {
        border-color: #b8b3ff;
        box-shadow: 0 2px 8px rgba(108, 99, 255, 0.1);
    }

    .field-card.selected {
        border-color: #6c63ff;
        box-shadow: 0 2px 12px rgba(108, 99, 255, 0.2);
    }

    .field-card.drag-over-field {
        border-top: 3px solid #6c63ff;
    }

    .field-card .drag-handle {
        cursor: grab;
        color: #a0aec0;
        font-size: 1.1rem;
    }

    .field-card .drag-handle:active {
        cursor: grabbing;
    }

    .field-card .field-info {
        flex: 1;
        min-width: 0;
    }

    .field-card .field-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .field-card .field-type-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        background: #eef0ff;
        color: #6c63ff;
        font-weight: 500;
    }

    .field-card .required-star {
        color: #e53e3e;
        font-weight: 700;
        cursor: pointer;
        font-size: 1rem;
    }

    .field-card .required-star.inactive {
        color: #d1d5db;
    }

    .field-card .btn-remove-field {
        background: none;
        border: none;
        color: #a0aec0;
        font-size: 1rem;
        cursor: pointer;
        padding: 0.2rem;
        border-radius: 4px;
        line-height: 1;
        transition: all 0.15s ease;
    }

    .field-card .btn-remove-field:hover {
        color: #e53e3e;
        background: #fff5f5;
    }

    /* ── Properties Panel (Right Sidebar) ── */
    .builder-properties {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        height: fit-content;
        position: sticky;
        top: 80px;
    }

    .builder-properties h6 {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #6c63ff;
    }

    .properties-empty {
        text-align: center;
        color: #a0aec0;
        padding: 2rem 1rem;
    }

    .properties-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .property-group {
        margin-bottom: 1rem;
    }

    .property-group label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.3rem;
        display: block;
    }

    .property-group .form-control,
    .property-group .form-select {
        font-size: 0.875rem;
    }

    /* ── Options Editor ── */
    .option-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
    }

    .option-row .form-control {
        flex: 1;
    }

    .option-row .btn-remove-option {
        background: none;
        border: none;
        color: #e53e3e;
        cursor: pointer;
        padding: 0.25rem;
        font-size: 0.9rem;
        line-height: 1;
    }

    .btn-add-option {
        font-size: 0.8rem;
        padding: 0.3rem 0.75rem;
    }

    /* ── Bottom Bar ── */
    .builder-bottom-bar {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* ── Preview Modal ── */
    .preview-form-content .form-group {
        margin-bottom: 1rem;
    }

    .preview-form-content label {
        font-weight: 600;
        margin-bottom: 0.3rem;
        display: block;
        font-size: 0.9rem;
    }

    .preview-form-content .required-indicator {
        color: #e53e3e;
        margin-left: 0.2rem;
    }

    /* ── Dark Mode Support ── */
    body.dark-mode .builder-toolbox,
    body.dark-mode .builder-canvas,
    body.dark-mode .builder-properties,
    body.dark-mode .builder-bottom-bar {
        background: #1a1a2e;
        border-color: #2d2d44;
    }

    body.dark-mode .builder-toolbox h6,
    body.dark-mode .builder-canvas h6,
    body.dark-mode .builder-properties h6 {
        color: #e2e8f0;
    }

    body.dark-mode .field-type {
        background: #16213e;
        border-color: #2d2d44;
        color: #c7c7d4;
    }

    body.dark-mode .field-type:hover {
        background: #1e2d4a;
        border-color: #6c63ff;
        color: #6c63ff;
    }

    body.dark-mode .field-card {
        background: #16213e;
        border-color: #2d2d44;
    }

    body.dark-mode .field-card:hover {
        border-color: #6c63ff;
    }

    body.dark-mode .field-card .field-label {
        color: #e2e8f0;
    }

    body.dark-mode .canvas-drop-zone {
        border-color: #2d2d44;
    }

    body.dark-mode .canvas-drop-zone.drag-over {
        border-color: #6c63ff;
        background: rgba(108, 99, 255, 0.1);
    }

    body.dark-mode .properties-empty {
        color: #6c6c80;
    }

    body.dark-mode .property-group label {
        color: #a0a0b8;
    }
</style>
@endpush

@section('content')

{{-- Main builder form --}}
<form
    id="builder-form"
    method="POST"
    action="{{ isset($form) ? route('admin.internal-forms.update', $form->id) : route('admin.internal-forms.store') }}"
>
    @csrf
    @if(isset($form))
        @method('PUT')
    @endif

    {{-- Hidden fields --}}
    <input type="hidden" name="title" value="{{ isset($form) ? $form->title : old('title', '') }}">
    <input type="hidden" name="description" value="{{ isset($form) ? $form->description : old('description', '') }}">
    <input type="hidden" name="status" value="{{ isset($form) ? $form->status : old('status', 'draft') }}">
    <input type="hidden" name="group_id" value="{{ isset($form) ? $form->group_id : old('group_id', '') }}">
    <input type="hidden" name="is_public" value="{{ isset($form) ? $form->is_public : old('is_public', 0) }}">
    <input type="hidden" name="allow_anonymous" value="{{ isset($form) ? $form->allow_anonymous : old('allow_anonymous', 0) }}">
    <input type="hidden" name="structure" id="structure-input" value="">

    {{-- Existing structure for loading in edit mode --}}
    @if(isset($form) && $form->structure)
        <input type="hidden" id="existing-structure" value="{{ json_encode($form->structure) }}">
    @endif

    <div class="row builder-container">

        {{-- ════════════════════════════════════════════════════
             LEFT SIDEBAR: Toolbox
             ════════════════════════════════════════════════════ --}}
        <div class="col-md-3">
            <div class="builder-toolbox" id="field-toolbox">
                <h6><i class="bi bi-tools me-2"></i>Champs disponibles</h6>

                <div class="field-type" draggable="true" data-type="text">
                    <i class="bi bi-input-cursor"></i>
                    <span>Texte court</span>
                </div>

                <div class="field-type" draggable="true" data-type="textarea">
                    <i class="bi bi-textarea-t"></i>
                    <span>Texte long</span>
                </div>

                <div class="field-type" draggable="true" data-type="email">
                    <i class="bi bi-envelope"></i>
                    <span>Email</span>
                </div>

                <div class="field-type" draggable="true" data-type="number">
                    <i class="bi bi-123"></i>
                    <span>Num&eacute;ro</span>
                </div>

                <div class="field-type" draggable="true" data-type="date">
                    <i class="bi bi-calendar"></i>
                    <span>Date</span>
                </div>

                <div class="field-type" draggable="true" data-type="radio">
                    <i class="bi bi-record-circle"></i>
                    <span>Choix unique</span>
                </div>

                <div class="field-type" draggable="true" data-type="checkbox">
                    <i class="bi bi-check-square"></i>
                    <span>Choix multiple</span>
                </div>

                <div class="field-type" draggable="true" data-type="select">
                    <i class="bi bi-list"></i>
                    <span>Liste d&eacute;roulante</span>
                </div>

                <div class="field-type" draggable="true" data-type="file">
                    <i class="bi bi-upload"></i>
                    <span>Upload fichier</span>
                </div>

                <div class="field-type" draggable="true" data-type="rating">
                    <i class="bi bi-star"></i>
                    <span>&Eacute;chelle de notation</span>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
             CENTER: Canvas / Preview
             ════════════════════════════════════════════════════ --}}
        <div class="col-md-6">
            <div class="builder-canvas">
                <h6><i class="bi bi-grid-3x3-gap me-2"></i>Aper&ccedil;u du formulaire</h6>

                <div id="form-canvas" class="canvas-drop-zone">
                    <div class="canvas-placeholder">
                        <i class="bi bi-download"></i>
                        <p>Glissez les champs ici</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
             RIGHT SIDEBAR: Properties Editor
             ════════════════════════════════════════════════════ --}}
        <div class="col-md-3">
            <div class="builder-properties" id="field-properties">
                <h6><i class="bi bi-sliders me-2"></i>Propri&eacute;t&eacute;s</h6>

                <div class="properties-empty">
                    <i class="bi bi-cursor"></i>
                    <p>S&eacute;lectionnez un champ pour modifier ses propri&eacute;t&eacute;s</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════
         BOTTOM BAR: Actions
         ════════════════════════════════════════════════════ --}}
    <div class="builder-bottom-bar">
        <a href="{{ isset($form) ? route('admin.internal-forms.show', $form->id) : route('admin.internal-forms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <button type="button" class="btn btn-outline-primary" id="btn-preview" data-bs-toggle="modal" data-bs-target="#previewModal">
            <i class="bi bi-eye me-1"></i>Aper&ccedil;u
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Sauvegarder
        </button>
    </div>

</form>

{{-- ════════════════════════════════════════════════════
     PREVIEW MODAL
     ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="bi bi-eye me-2"></i>Aper&ccedil;u du formulaire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div id="preview-content" class="preview-form-content">
                    <p class="text-muted text-center">Aucun champ ajout&eacute;</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/form-builder.js') }}"></script>
@endpush
