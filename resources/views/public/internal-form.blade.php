@extends('layouts.public')

@section('title', $form->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <h1 class="mb-3">{{ $form->title }}</h1>
            @if($form->description)
                <p class="text-muted mb-4">{{ $form->description }}</p>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('public.form.internal.submit', $form->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @foreach($form->fields->sortBy('order') as $field)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ $field->label }}
                                    @if($field->required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @switch($field->type)
                                    @case('text')
                                        <input type="text"
                                               name="responses[{{ $field->id }}]"
                                               class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                               placeholder="{{ $field->placeholder ?? '' }}"
                                               value="{{ old('responses.' . $field->id) }}"
                                               {{ $field->required ? 'required' : '' }}>
                                        @break

                                    @case('textarea')
                                        <textarea name="responses[{{ $field->id }}]"
                                                  class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="{{ $field->placeholder ?? '' }}"
                                                  {{ $field->required ? 'required' : '' }}>{{ old('responses.' . $field->id) }}</textarea>
                                        @break

                                    @case('email')
                                        <input type="email"
                                               name="responses[{{ $field->id }}]"
                                               class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                               placeholder="{{ $field->placeholder ?? '' }}"
                                               value="{{ old('responses.' . $field->id) }}"
                                               {{ $field->required ? 'required' : '' }}>
                                        @break

                                    @case('number')
                                        <input type="number"
                                               name="responses[{{ $field->id }}]"
                                               class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                               placeholder="{{ $field->placeholder ?? '' }}"
                                               value="{{ old('responses.' . $field->id) }}"
                                               {{ $field->required ? 'required' : '' }}>
                                        @break

                                    @case('date')
                                        <input type="date"
                                               name="responses[{{ $field->id }}]"
                                               class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                               value="{{ old('responses.' . $field->id) }}"
                                               {{ $field->required ? 'required' : '' }}>
                                        @break

                                    @case('radio')
                                        @foreach($field->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input @error('responses.' . $field->id) is-invalid @enderror"
                                                       type="radio"
                                                       name="responses[{{ $field->id }}]"
                                                       id="field_{{ $field->id }}_{{ $loop->index }}"
                                                       value="{{ $option }}"
                                                       {{ old('responses.' . $field->id) === $option ? 'checked' : '' }}
                                                       {{ $field->required ? 'required' : '' }}>
                                                <label class="form-check-label" for="field_{{ $field->id }}_{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @break

                                    @case('checkbox')
                                        @foreach($field->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input @error('responses.' . $field->id) is-invalid @enderror"
                                                       type="checkbox"
                                                       name="responses[{{ $field->id }}][]"
                                                       id="field_{{ $field->id }}_{{ $loop->index }}"
                                                       value="{{ $option }}"
                                                       {{ is_array(old('responses.' . $field->id)) && in_array($option, old('responses.' . $field->id)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="field_{{ $field->id }}_{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @break

                                    @case('select')
                                        <select name="responses[{{ $field->id }}]"
                                                class="form-select @error('responses.' . $field->id) is-invalid @enderror"
                                                {{ $field->required ? 'required' : '' }}>
                                            <option value="">-- Choisir --</option>
                                            @foreach($field->options as $option)
                                                <option value="{{ $option }}" {{ old('responses.' . $field->id) === $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('file')
                                        <input type="file"
                                               name="responses[{{ $field->id }}]"
                                               class="form-control @error('responses.' . $field->id) is-invalid @enderror"
                                               {{ $field->required ? 'required' : '' }}>
                                        @break

                                    @case('rating')
                                        <div class="rating-group d-flex gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <input type="radio"
                                                       name="responses[{{ $field->id }}]"
                                                       id="rating_{{ $field->id }}_{{ $i }}"
                                                       value="{{ $i }}"
                                                       class="d-none rating-input"
                                                       {{ old('responses.' . $field->id) == $i ? 'checked' : '' }}
                                                       {{ $field->required ? 'required' : '' }}>
                                                <label for="rating_{{ $field->id }}_{{ $i }}"
                                                       class="rating-star"
                                                       style="cursor: pointer; font-size: 1.5rem; color: #ffc107;">
                                                    <i class="bi bi-star{{ old('responses.' . $field->id) >= $i ? '-fill' : '' }}"></i>
                                                </label>
                                            @endfor
                                        </div>
                                        @break
                                @endswitch

                                @error('responses.' . $field->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.rating-group').forEach(group => {
        const labels = group.querySelectorAll('.rating-star');
        labels.forEach((label, index) => {
            label.addEventListener('click', () => {
                labels.forEach((l, i) => {
                    const icon = l.querySelector('i');
                    if (i <= index) {
                        icon.className = 'bi bi-star-fill';
                    } else {
                        icon.className = 'bi bi-star';
                    }
                });
            });
            label.addEventListener('mouseenter', () => {
                labels.forEach((l, i) => {
                    const icon = l.querySelector('i');
                    if (i <= index) {
                        icon.className = 'bi bi-star-fill';
                    } else {
                        icon.className = 'bi bi-star';
                    }
                });
            });
        });
        group.addEventListener('mouseleave', () => {
            const checked = group.querySelector('.rating-input:checked');
            const checkedIndex = checked ? parseInt(checked.value) - 1 : -1;
            labels.forEach((l, i) => {
                const icon = l.querySelector('i');
                if (i <= checkedIndex) {
                    icon.className = 'bi bi-star-fill';
                } else {
                    icon.className = 'bi bi-star';
                }
            });
        });
    });
</script>
@endpush
@endsection
