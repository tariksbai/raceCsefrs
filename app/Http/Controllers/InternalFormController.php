<?php

namespace App\Http\Controllers;

use App\Models\InternalForm;
use App\Models\FormAccessLog;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalFormController extends Controller
{
    public function index(Request $request)
    {
        $query = InternalForm::with(['group', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        $forms = $query->latest()->paginate(15)->withQueryString();
        $groups = Group::all();

        return view('internal-forms.index', compact('forms', 'groups'));
    }

    public function create()
    {
        $groups = Group::all();

        return view('internal-forms.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
            'is_public' => 'boolean',
            'structure' => 'nullable|json',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,draft',
            'allow_anonymous' => 'boolean',
        ]);

        $data = $request->only('title', 'description', 'group_id', 'is_public', 'structure', 'start_date', 'end_date', 'status', 'allow_anonymous');
        $data['created_by'] = Auth::id();

        InternalForm::create($data);

        return redirect()->route('admin.internal-forms.index')->with('success', 'Formulaire interne créé avec succès.');
    }

    public function show($id)
    {
        $form = InternalForm::with(['group', 'creator', 'fields', 'responses'])->findOrFail($id);

        FormAccessLog::create([
            'form_type' => 'internal',
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'action' => 'view',
        ]);

        return view('internal-forms.show', compact('form'));
    }

    public function edit($id)
    {
        $form = InternalForm::with('fields')->findOrFail($id);
        $groups = Group::all();

        return view('internal-forms.edit', compact('form', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $form = InternalForm::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
            'is_public' => 'boolean',
            'structure' => 'nullable|json',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,draft',
            'allow_anonymous' => 'boolean',
        ]);

        $form->update($request->only('title', 'description', 'group_id', 'is_public', 'structure', 'start_date', 'end_date', 'status', 'allow_anonymous'));

        return redirect()->route('admin.internal-forms.index')->with('success', 'Formulaire interne mis à jour avec succès.');
    }

    public function destroy($id)
    {
        InternalForm::findOrFail($id)->delete();

        return redirect()->route('admin.internal-forms.index')->with('success', 'Formulaire interne supprimé avec succès.');
    }

    public function builder($id)
    {
        $form = InternalForm::with('fields')->findOrFail($id);
        $groups = Group::all();

        return view('internal-forms.builder', compact('form', 'groups'));
    }
}
