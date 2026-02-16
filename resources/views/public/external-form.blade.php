@extends('layouts.public')

@section('title', $form->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-3">{{ $form->title }}</h1>
            @if($form->description)
                <p class="text-muted mb-4">{{ $form->description }}</p>
            @endif
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        {!! $form->iframe_code !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
