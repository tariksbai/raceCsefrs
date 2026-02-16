@extends('layouts.app')

@section('title', 'Statistiques - ' . $form->title)
@section('page-title', 'Statistiques - ' . $form->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.responses.index', $form->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour aux r&eacute;ponses
    </a>
</div>

{{-- Summary Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total r&eacute;ponses</p>
                        <h3 class="mb-0 fw-bold">{{ $statistics['total'] ?? 0 }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-chat-square-text" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">R&eacute;ponses aujourd'hui</p>
                        <h3 class="mb-0 fw-bold">{{ $statistics['today'] ?? 0 }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">R&eacute;ponses cette semaine</p>
                        <h3 class="mb-0 fw-bold">{{ $statistics['this_week'] ?? 0 }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-calendar-week" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">R&eacute;ponses ce mois</p>
                        <h3 class="mb-0 fw-bold">{{ $statistics['this_month'] ?? 0 }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-calendar-month" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Responses Over Time Chart --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-graph-up me-2"></i> R&eacute;ponses dans le temps
        </h5>
    </div>
    <div class="card-body">
        <canvas id="responsesChart" height="100"></canvas>
    </div>
</div>

{{-- Per-Field Breakdown --}}
<h5 class="mb-3">
    <i class="bi bi-list-columns-reverse me-2"></i> D&eacute;tail par champ
</h5>

@if(isset($statistics['fields']) && count($statistics['fields']) > 0)
    <div class="row g-4">
        @foreach($statistics['fields'] as $index => $fieldStat)
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">
                            <i class="bi bi-input-cursor-text me-1"></i>
                            {{ $fieldStat['label'] ?? 'Champ #' . ($index + 1) }}
                        </h6>
                        <span class="badge bg-secondary">{{ $fieldStat['type'] ?? 'text' }}</span>
                    </div>
                    <div class="card-body">
                        @php
                            $fieldType = $fieldStat['type'] ?? 'text';
                        @endphp

                        {{-- Radio / Checkbox / Select: Pie Chart --}}
                        @if(in_array($fieldType, ['radio', 'checkbox', 'select']))
                            @if(!empty($fieldStat['distribution']))
                                <canvas id="fieldChart{{ $index }}" height="200"></canvas>
                            @else
                                <p class="text-muted text-center mb-0">Aucune donn&eacute;e disponible.</p>
                            @endif

                        {{-- Number: Min, Max, Average --}}
                        @elseif($fieldType === 'number')
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <p class="text-muted small mb-1">Minimum</p>
                                        <h4 class="mb-0 text-primary">{{ $fieldStat['min'] ?? 'N/A' }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <p class="text-muted small mb-1">Maximum</p>
                                        <h4 class="mb-0 text-danger">{{ $fieldStat['max'] ?? 'N/A' }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <p class="text-muted small mb-1">Moyenne</p>
                                        <h4 class="mb-0 text-success">
                                            {{ isset($fieldStat['average']) ? number_format($fieldStat['average'], 2) : 'N/A' }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                        {{-- Rating: Average with Star Display --}}
                        @elseif($fieldType === 'rating')
                            <div class="text-center">
                                <h2 class="mb-2">
                                    {{ isset($fieldStat['average']) ? number_format($fieldStat['average'], 1) : 'N/A' }}
                                </h2>
                                <div class="mb-2">
                                    @php
                                        $avg = $fieldStat['average'] ?? 0;
                                        $fullStars = floor($avg);
                                        $halfStar = ($avg - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                    @endphp
                                    @for($i = 0; $i < $fullStars; $i++)
                                        <i class="bi bi-star-fill text-warning" style="font-size: 1.5rem;"></i>
                                    @endfor
                                    @if($halfStar)
                                        <i class="bi bi-star-half text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                    @for($i = 0; $i < $emptyStars; $i++)
                                        <i class="bi bi-star text-warning" style="font-size: 1.5rem;"></i>
                                    @endfor
                                </div>
                                <p class="text-muted small mb-0">
                                    Bas&eacute; sur {{ $fieldStat['count'] ?? 0 }} r&eacute;ponse(s)
                                </p>
                            </div>

                        {{-- Text / Textarea / Email: Sample Responses --}}
                        @elseif(in_array($fieldType, ['text', 'textarea', 'email']))
                            @if(!empty($fieldStat['samples']))
                                <p class="text-muted small mb-2">Exemples de r&eacute;ponses :</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($fieldStat['samples'] as $sample)
                                        <li class="list-group-item px-0">
                                            <i class="bi bi-quote me-1 text-muted"></i>
                                            {{ Str::limit($sample, 150) }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-center mb-0">Aucune r&eacute;ponse enregistr&eacute;e.</p>
                            @endif

                        {{-- Default fallback --}}
                        @else
                            @if(!empty($fieldStat['samples']))
                                <p class="text-muted small mb-2">Exemples de r&eacute;ponses :</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($fieldStat['samples'] as $sample)
                                        <li class="list-group-item px-0">
                                            {{ Str::limit($sample, 150) }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-center mb-0">Aucune donn&eacute;e disponible.</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">Aucune statistique disponible pour les champs de ce formulaire.</p>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── Responses Over Time Line Chart ──
        const responsesCtx = document.getElementById('responsesChart');
        if (responsesCtx) {
            const timeLabels = @json($statistics['chart']['labels'] ?? []);
            const timeData = @json($statistics['chart']['data'] ?? []);

            new Chart(responsesCtx, {
                type: 'line',
                data: {
                    labels: timeLabels,
                    datasets: [{
                        label: 'Réponses',
                        data: timeData,
                        borderColor: 'rgba(108, 99, 255, 1)',
                        backgroundColor: 'rgba(108, 99, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(108, 99, 255, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // ── Per-Field Pie Charts (radio / checkbox / select) ──
        const fieldColors = [
            'rgba(108, 99, 255, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(46, 204, 113, 0.8)',
            'rgba(231, 76, 60, 0.8)',
            'rgba(52, 152, 219, 0.8)'
        ];

        const fieldsData = @json($statistics['fields'] ?? []);

        fieldsData.forEach(function (fieldStat, index) {
            const fieldType = fieldStat.type || 'text';
            if (['radio', 'checkbox', 'select'].includes(fieldType) && fieldStat.distribution) {
                const canvasEl = document.getElementById('fieldChart' + index);
                if (canvasEl) {
                    const labels = Object.keys(fieldStat.distribution);
                    const data = Object.values(fieldStat.distribution);

                    new Chart(canvasEl, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: fieldColors.slice(0, labels.length),
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const total = context.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                            const value = context.parsed;
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return context.label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    });
</script>
@endpush
