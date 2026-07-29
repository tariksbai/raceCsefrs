<?php

namespace App\Http\Controllers;

use App\Models\ExternalForm;
use App\Models\FormAccessLog;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalFormController extends Controller
{
    public function index(Request $request)
    {
        $query = ExternalForm::with(['group', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        $forms = $query->latest()->paginate(15)->withQueryString();
        $groups = Group::all();

        return view('external-forms.index', compact('forms', 'groups'));
    }

    public function create()
    {
        $groups = Group::all();

        return view('external-forms.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
            'is_public' => 'boolean',
            'iframe_code' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only('title', 'description', 'group_id', 'is_public', 'iframe_code', 'start_date', 'end_date', 'status');
        $data['created_by'] = Auth::id();

        ExternalForm::create($data);

        return redirect()->route('admin.external-forms.index')->with('success', 'Formulaire externe créé avec succès.');
    }

    public function show($id)
    {
        $form = ExternalForm::with(['group', 'creator'])->findOrFail($id);

        FormAccessLog::create([
            'form_type' => 'external',
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'action' => 'view',
        ]);

        return view('external-forms.show', compact('form'));
    }

    public function edit($id)
    {
        $form = ExternalForm::findOrFail($id);
        $groups = Group::all();

        return view('external-forms.edit', compact('form', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $form = ExternalForm::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
            'is_public' => 'boolean',
            'iframe_code' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        $form->update($request->only('title', 'description', 'group_id', 'is_public', 'iframe_code', 'start_date', 'end_date', 'status'));

        return redirect()->route('admin.external-forms.index')->with('success', 'Formulaire externe mis à jour avec succès.');
    }

    public function destroy($id)
    {
        ExternalForm::findOrFail($id)->delete();

        return redirect()->route('admin.external-forms.index')->with('success', 'Formulaire externe supprimé avec succès.');
    }
}
