<?php

namespace App\Http\Controllers;

use App\Models\ExternalForm;
use App\Models\InternalForm;
use App\Models\InternalFormResponse;
use App\Models\InternalFormResponseValue;
use App\Models\FormAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicFormController extends Controller
{
    public function showExternal($id)
    {
        $form = ExternalForm::with('group')->findOrFail($id);

        if (!$form->is_public) {
            if (!Auth::check()) {
                abort(403, 'Accès non autorisé à ce formulaire.');
            }

            $user = Auth::user();
            if ($form->group_id && !$user->groups->contains($form->group_id) && !$user->isAdmin()) {
                abort(403, 'Accès non autorisé à ce formulaire.');
            }
        }

        FormAccessLog::create([
            'form_type' => 'external',
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'action' => 'public_view',
        ]);

        return view('public.external-form', compact('form'));
    }

    public function showInternal($id)
    {
        $form = InternalForm::with('fields')->findOrFail($id);

        FormAccessLog::create([
            'form_type' => 'internal',
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'action' => 'public_view',
        ]);

        return view('public.internal-form', compact('form'));
    }

    public function submitInternal(Request $request, $id)
    {
        $form = InternalForm::with('fields')->findOrFail($id);

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

        return redirect()->back()->with('success', 'Votre réponse a été enregistrée avec succès. Merci pour votre participation.');
    }
}
