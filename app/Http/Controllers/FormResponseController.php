<?php

namespace App\Http\Controllers;

use App\Models\InternalForm;
use App\Models\InternalFormResponse;
use App\Models\InternalFormResponseValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormResponseController extends Controller
{
    public function index($formId)
    {
        $form = InternalForm::findOrFail($formId);
        $responses = InternalFormResponse::where('form_id', $formId)
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('responses.index', compact('form', 'responses'));
    }

    public function show($formId, $responseId)
    {
        $form = InternalForm::findOrFail($formId);
        $response = InternalFormResponse::where('form_id', $formId)
            ->with(['user', 'values.field'])
            ->findOrFail($responseId);

        return view('responses.show', compact('form', 'response'));
    }

    public function store(Request $request, $formId)
    {
        $form = InternalForm::with('fields')->findOrFail($formId);

        $response = InternalFormResponse::create([
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        foreach ($form->fields as $field) {
            $value = $request->input('field_' . $field->id);

            if ($value !== null) {
                InternalFormResponseValue::create([
                    'response_id' => $response->id,
                    'field_id' => $field->id,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        }

        return redirect()->route('admin.responses.index', $formId)->with('success', 'Réponse enregistrée avec succès.');
    }

    public function exportCsv($formId)
    {
        $form = InternalForm::with('fields')->findOrFail($formId);
        $responses = InternalFormResponse::where('form_id', $formId)
            ->with('values.field')
            ->get();

        $filename = 'responses_' . $form->id . '_' . now()->format('Y-m-d_His') . '.csv';

        $callback = function () use ($form, $responses) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $headers = ['ID', 'Utilisateur', 'Adresse IP', 'Date de soumission'];
            foreach ($form->fields as $field) {
                $headers[] = $field->label;
            }
            fputcsv($handle, $headers, ';');

            // Data rows
            foreach ($responses as $response) {
                $row = [
                    $response->id,
                    $response->user ? $response->user->name : 'Anonyme',
                    $response->ip_address,
                    $response->created_at->format('d/m/Y H:i:s'),
                ];

                foreach ($form->fields as $field) {
                    $value = $response->values->firstWhere('field_id', $field->id);
                    $row[] = $value ? $value->value : '';
                }

                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel($formId)
    {
        $form = InternalForm::with('fields')->findOrFail($formId);
        $responses = InternalFormResponse::where('form_id', $formId)
            ->with('values.field')
            ->get();

        $filename = 'responses_' . $form->id . '_' . now()->format('Y-m-d_His') . '.xlsx';

        $callback = function () use ($form, $responses) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $headers = ['ID', 'Utilisateur', 'Adresse IP', 'Date de soumission'];
            foreach ($form->fields as $field) {
                $headers[] = $field->label;
            }
            fputcsv($handle, $headers, ';');

            // Data rows
            foreach ($responses as $response) {
                $row = [
                    $response->id,
                    $response->user ? $response->user->name : 'Anonyme',
                    $response->ip_address,
                    $response->created_at->format('d/m/Y H:i:s'),
                ];

                foreach ($form->fields as $field) {
                    $value = $response->values->firstWhere('field_id', $field->id);
                    $row[] = $value ? $value->value : '';
                }

                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function statistics($formId)
    {
        $form = InternalForm::with('fields')->findOrFail($formId);
        $totalResponses = InternalFormResponse::where('form_id', $formId)->count();
        $responses = InternalFormResponse::where('form_id', $formId)
            ->with('values.field')
            ->get();

        $fieldStatistics = [];
        foreach ($form->fields as $field) {
            $values = InternalFormResponseValue::where('field_id', $field->id)->pluck('value');
            $fieldStatistics[$field->id] = [
                'field' => $field,
                'total' => $values->count(),
                'values' => $values,
            ];
        }

        return view('responses.statistics', compact('form', 'totalResponses', 'fieldStatistics'));
    }
}
